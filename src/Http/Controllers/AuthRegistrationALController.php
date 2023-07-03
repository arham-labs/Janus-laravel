<?php

namespace Arhamlabs\Authentication\Http\Controllers;

use Arhamlabs\ApiResponse\ApiResponse;
use Arhamlabs\Authentication\Interfaces\AuthLoginALInterface;
use Arhamlabs\Authentication\Interfaces\AuthRegistrationALInterface;
use Arhamlabs\Authentication\Models\AuthSetting;
use App\Models\User;
use Arhamlabs\Authentication\Services\RegistrationValidationService;
use Arhamlabs\Authentication\Services\TokenService;
use Arhamlabs\Authentication\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthRegistrationALController extends Controller
{
    private $authRegistrationALRepository;
    private $authLoginALRepository;
    private $apiResponse;
    public $userService;
    public $tokenService;
    public $registrationValidationService;
    public function __construct(
        AuthRegistrationALInterface $AuthRegistrationALInterface,
        AuthLoginALInterface $authLoginALInterface,
        ApiResponse $apiResponse,
        UserService $userService,
        TokenService $tokenService,
        RegistrationValidationService $registrationValidationService,
    ) {
        $this->authRegistrationALRepository = $AuthRegistrationALInterface;
        $this->authLoginALRepository = $authLoginALInterface;
        $this->apiResponse = $apiResponse;
        $this->userService = $userService;
        $this->tokenService = $tokenService;
        $this->registrationValidationService = $registrationValidationService;
    }

    //User registration using email,password
    public function registerUser(Request $request)
    {
        try {
            $validate = $this->registrationValidationService->validation($request);
            if (!empty($validate)) {
                $this->apiResponse->setCustomErrors($validate);
                throw new Exception('Validation error', 422);
            }

            //user registration in temporary table 
            $details = $this->authRegistrationALRepository->register($request);
            if ($details['status'] == 'success') {
                // if email_verification and mail_send flags set to be true then mail will be send 
                if (config('al_auth_config.email_verification') === true) {
                    Log::info('mail');
                    $this->userService->SendMailVerificationService($details['data']);
                }
                // if email_verification flag set to be false then data will be added to User table
                if (config('al_auth_config.email_verification') === false) {

                    /*
                    create user row into main table
                    example-
                    CreateMainTableEntry(request data,model object)

                    */
                    $user = new User;
                    $createMainTableEntry = $this->authLoginALRepository->CreateMainTableEntry($request, $user);
                    if ($createMainTableEntry['status'] == 'success') {
                        $customUserMessageTitle = __('messages.register_success_title');
                        $customUserMessageText = __('messages.register_success_text');
                        $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                    } else {
                        $customUserMessageTitle = __('error_messages.system_error');
                        $this->apiResponse->setCustomResponse($customUserMessageTitle);
                        throw new Exception($customUserMessageTitle, 500);
                    }
                }
            } else {
                $customUserMessageTitle = __('error_messages.system_error');
                $this->apiResponse->setCustomResponse($customUserMessageTitle);
                throw new Exception($customUserMessageTitle, 500);
            }
            return $this->apiResponse->getResponse(200);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $errorResponseMessage = $errorMessage != null ? $errorMessage : __('error_messages.system_error');
            return $this->apiResponse->getResponse($e->getCode(), null, $errorResponseMessage);
        }
    }


    //User registration using mobile,otp
    public function registerMobileUser(Request $request)
    {
        try {
            Log::info('registration via mobile otp initiated');
            $validate = $this->registrationValidationService->validation($request);
            if (!empty($validate)) {
                $this->apiResponse->setCustomErrors($validate);
                throw new Exception('Validation error', 422);
            }

            //user registration in temporary table 
            $tempDetails = $this->authRegistrationALRepository->register($request);
            if ($tempDetails['status'] == 'success') {
                // if mobile_verification flags set to be true then sms will be send 
                if (config('al_auth_config.mobile_verification') === true) {
                    Log::info('registration via mobile otp');
                    if (config('alNotificationConfig.enable_notification') === true && config('alNotificationConfig.notification_type.sms')) {
                        $details = $this->authLoginALRepository->sentMobileOtp($request);
                        if ($details['status'] == 'success') {
                            Log::info('Login mobile otp');
                            $customUserMessageTitle = __('messages.otp_send_success_title');
                            $customUserMessageText = __('messages.otp_send_success_text');
                            $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                        } else if ($details['status'] == 'error' && $details['error'] == 'delay') {
                            $customUserMessageTitle = __('error_messages.delay_otp_mobile_title', ['delay' => config('al_auth_config.sms.delay') ? config('al_auth_config.sms.delay') : 60]);
                            $customUserMessageText = __('error_messages.delay_otp_mobile_text', ['delay' => config('al_auth_config.sms.delay') ? config('al_auth_config.sms.delay') : 60]);
                            $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                            throw new Exception($customUserMessageTitle, 422);
                        } else if ($details['status'] == 'error' && $details['error'] == 'day_limit_error') {
                            $customUserMessageTitle = __('error_messages.day_limit_error_otp_mobile_title', ['day_limit_error' => config('al_auth_config.sms.day_limit_error') ? config('al_auth_config.sms.day_limit_error') : 60]);
                            $customUserMessageText = __('error_messages.day_limit_error_otp_mobile_text', ['day_limit_error' => config('al_auth_config.sms.day_limit_error') ? config('al_auth_config.sms.day_limit_error') : 60]);
                            $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                            throw new Exception($customUserMessageTitle, 422);
                        } else {
                            $customUserMessageTitle = __('error_messages.invalid_mobile_title');
                            $customUserMessageText = __('error_messages.invalid_mobile_text');
                            $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                            throw new Exception($customUserMessageTitle, 422);
                        }
                    } else {
                        $customUserMessageTitle = __('error_messages.sms_service_unavailable_title');
                        $customUserMessageText = __('error_messages.sms_service_unavailable_text');
                        $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                        throw new Exception('SMS service disabled.You can enabled it from config.', 401);
                    }
                }
                // if email_verification flag set to be false then data will be added to User table
                if (config('al_auth_config.mobile_verification') === false) {

                    /*
                    create user row into main table
                    example-
                    CreateMainTableEntry(request data,model object)

                    */
                    $user = new User;
                    $createMainTableEntry = $this->authLoginALRepository->CreateMainTableEntry($request, $user);
                    if ($createMainTableEntry['status'] == 'success') {
                        //update user registration in temporary table to verify
                        $tempDetails = $this->authRegistrationALRepository->verifyTemporaryRegistration($tempDetails['data']);
                        $customUserMessageTitle = __('messages.register_success_title');
                        $customUserMessageText = __('messages.register_success_text');
                        $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                    } else {
                        $customUserMessageTitle = __('error_messages.system_error');
                        $this->apiResponse->setCustomResponse($customUserMessageTitle);
                        throw new Exception($customUserMessageTitle, 500);
                    }
                }
            } else {
                $customUserMessageTitle = __('error_messages.system_error');
                $this->apiResponse->setCustomResponse($customUserMessageTitle);
                throw new Exception($customUserMessageTitle, 500);
            }
            return $this->apiResponse->getResponse(200);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $errorResponseMessage = $errorMessage != null ? $errorMessage : __('error_messages.system_error');
            return $this->apiResponse->getResponse($e->getCode(), null, $errorResponseMessage);
        }
    }

    //verify sms-otp for register mobile
    public function registrationMobileVerifyOtp(Request $request)
    {
        try {
            //check syntax validation for mobile & country code
            $this->registrationValidationService->checkRegistrationMobileValidation($request);
            Log::info('Verify otp');
            //validate otp 
            $validateOtpResponse = $this->authLoginALRepository->checkSmsOtp($request->mobile, $request->country_code, $request->otp);
            // dd($validateOtpResponse);
            if ($validateOtpResponse['status'] == 'validate') {
                //get user details using username/email/mobile
                $user = $this->authLoginALRepository->getUserByMobile($request->mobile, $request->country_code);

                if (!empty($user)) {
                    $customUserMessageTitle = __('error_messages.exist_mobile_title');
                    $customUserMessageText = __('error_messages.exist_mobile_text');
                    $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                    throw new Exception($customUserMessageTitle, 401);
                }

                $tempUser = $this->authLoginALRepository->getTempUserByMobile($request->mobile, $request->country_code);
                if (empty($tempUser)) {
                    $customUserMessageTitle = __('error_messages.bad_request');
                    $this->apiResponse->setCustomResponse($customUserMessageTitle);
                    throw new Exception($customUserMessageTitle, 400);
                }
                $user = new User;
                $userDetails = $this->authLoginALRepository->CreateMainTableEntry($tempUser, $user);
                if ($userDetails['status'] === 'success') {
                    Log::info('user details added to user table');
                    $user = $userDetails['data'];
                    //update user registration in temporary table to verify
                    $tempDetails = $this->authRegistrationALRepository->verifyTemporaryRegistration($tempUser);
                } else {
                    $customUserMessageTitle = __('error_messages.system_error');
                    $this->apiResponse->setCustomResponse($customUserMessageTitle);
                    throw new Exception($customUserMessageTitle, 500);
                }
                $model_name = $user->getMorphClass();
                $userSettingDetails = AuthSetting::where('model_name', $model_name)->where('model_id', $user->id)->latest()->first();

                $ability =  $userSettingDetails->user_type ? 'userType:' . $userSettingDetails->user_type : 'userType:' .  config('al_auth_config.user_Type');
                $apiToken = $this->tokenService->generateSanctumToken($user, $ability);
                $customUserMessageTitle = __('messages.register_success_title');
                $customUserMessageText = __('messages.register_success_text');
                $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                $data = [
                    'accessToken' => $apiToken,
                    'user' => $user
                ];
                return $this->apiResponse->getResponse(200, $data);
            } else {
                $customUserMessageTitle = $validateOtpResponse['customUserMessageTitle'];
                $customUserMessageText = $validateOtpResponse['customUserMessageText'];
                $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                throw new Exception($customUserMessageTitle, 401);
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $errorLine = $e->getLine();
            $errorFile = $e->getFile();
            $errorResponseMessage = $errorMessage != null ? $errorMessage :  __('error_messages.system_error');
            return $this->apiResponse->getResponse($e->getCode(), null,  $errorResponseMessage, $errorFile, $errorLine);
        }
    }
    
}
