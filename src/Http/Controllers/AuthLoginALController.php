<?php

namespace Arhamlabs\Authentication\Http\Controllers;

use Arhamlabs\ApiResponse\ApiResponse;
use Arhamlabs\Authentication\Interfaces\AuthLoginALInterface;
use Arhamlabs\Authentication\Interfaces\AuthRegistrationALInterface;
use Arhamlabs\Authentication\Models\AuthSetting;
use Arhamlabs\Authentication\Models\AuthUser;
use Arhamlabs\Authentication\Services\LoginValidationService;
use Arhamlabs\Authentication\Services\TokenService;
use Arhamlabs\Authentication\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Arhamlabs\Authentication\Models\PasswordReset;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class AuthLoginALController extends Controller
{
    private $authLoginALRepository;
    private $authRegistrationALRepository;
    private $apiResponse;
    public $userService;
    public $tokenService;
    public $loginValidationService;
    public function __construct(
        AuthLoginALInterface $authLoginALRepository,
        AuthRegistrationALInterface $authRegistrationALRepository,
        ApiResponse $apiResponse,
        UserService $userService,
        TokenService $tokenService,
        LoginValidationService $loginValidationService,
    ) {
        $this->authLoginALRepository = $authLoginALRepository;
        $this->authRegistrationALRepository = $authRegistrationALRepository;
        $this->apiResponse = $apiResponse;
        $this->userService = $userService;
        $this->tokenService = $tokenService;
        $this->loginValidationService = $loginValidationService;
    }

    //User login using email,password
    public function login(Request $request)
    {
        try {
            $username = $request->input('username');
            $password = $request->input('password');

            //check valid email and username
            $this->loginValidationService->checkEmailOrUsername($username);

            //check valid password
            $this->loginValidationService->checkPassword($password);

            //get user details by email/mobile/username
            $user = $this->authLoginALRepository->getUserByEMU($username);

            $data = array();
            if ($user != null && $user->exists() === true) {
                if (Hash::check($password, $user->password) === true) {

                    //check user if blocked
                    if (config('al_auth_config.is_check_user_block') === true) {
                        if (!empty($user->settings) && $user->settings->user_status === 2) {
                            $customUserMessageTitle = __('error_messages.account_blocked_title');
                            $customUserMessageText = __('error_messages.account_blocked_text');
                            $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                            throw new Exception(__('error_messages.system_user_account_block'), 401);
                        }
                    }
                    $user_type = config('al_auth_config.user_Type') ? config('al_auth_config.user_Type') : 'app_user';
                    //get user type
                    if (!empty($user->settings) && !empty($user->settings->user_type)) {
                        $user_type = $user->settings->user_type;
                    }
                    $ability = 'userType:' . $user_type;
                    $apiToken = $this->tokenService->generateSanctumToken($user, $ability);
                    $request['last_login_at'] = Carbon::now();
                    $this->authLoginALRepository->updateAuthUserSetting($user, $request);
                    $customUserMessageTitle = __('messages.login_success_title');
                    $customUserMessageText = __('messages.login_success_text');
                    $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                    $data = [
                        'accessToken' => $apiToken,
                        'user' => $user
                    ];
                } else {
                    $customUserMessageTitle = __('error_messages.invalid_password_title');
                    $customUserMessageText = __('error_messages.invalid_password_text');
                    $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                    throw new Exception($customUserMessageTitle, 401);
                }
            } else {
                $customUserMessageTitle = __('error_messages.invalid_credentials_title');
                $customUserMessageText = __('error_messages.invalid_credentials_text');
                $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                throw new Exception($customUserMessageTitle, 401);
            }
            return $this->apiResponse->getResponse(200, $data);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $errorLine = $e->getLine();
            $errorFile = $e->getFile();
            $errorResponseMessage = $errorMessage != null ? $errorMessage :  __('error_messages.system_error');
            return $this->apiResponse->getResponse($e->getCode(), null,  $errorResponseMessage, $errorFile, $errorLine);
        }
    }


    //User sent otp on mobile
    public function sentMobileOtp(Request $request)
    {
        try {
            //check syntax validation for mobile & country code
            $this->loginValidationService->checkMobileValidation($request);
            if (config('alNotificationConfig.enable_notification') === true && config('alNotificationConfig.notification_type.sms')) {
                $details = $this->authLoginALRepository->sentMobileOtp($request);
                if ($details === true) {
                    Log::info('Login mobile otp');
                    $customUserMessageTitle = __('messages.otp_send_success_title');
                    $customUserMessageText = __('messages.otp_send_success_text');
                    $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                } else {
                    $customUserMessageTitle = __('error_messages.invalid_mobile_title');
                    $customUserMessageText = __('error_messages.invalid_mobile_text');
                    $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                    throw new Exception($customUserMessageTitle, 401);
                }
            } else {
                $customUserMessageTitle = __('error_messages.sms_service_unavailable_title');
                $customUserMessageText = __('error_messages.sms_service_unavailable_text');
                $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                throw new Exception($customUserMessageTitle, 401);
            }


            return $this->apiResponse->getResponse(200);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $errorLine = $e->getLine();
            $errorFile = $e->getFile();
            $errorResponseMessage = $errorMessage != null ? $errorMessage :  __('error_messages.system_error');
            return $this->apiResponse->getResponse($e->getCode(), null,  $errorResponseMessage, $errorFile, $errorLine);
        }
    }

    //User sent otp on email
    public function sentEmailOtp(Request $request)
    {
        try {
            //check valid email validation
            $this->loginValidationService->checkEmailValidation($request->email);
            $details = $this->authLoginALRepository->sentEmailOtp($request->email);
            if ($details === true) {
                $customUserMessageTitle = __('messages.otp_send_success_title');
                $customUserMessageText = __('messages.otp_send_success_text');
                $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
            } else {
                $customUserMessageTitle = __('error_messages.system_error');
                $customUserMessageText = __('error_messages.system_error');
                $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                throw new Exception($customUserMessageTitle, 401);
            }
            return $this->apiResponse->getResponse(200);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $errorLine = $e->getLine();
            $errorFile = $e->getFile();
            $errorResponseMessage = $errorMessage != null ? $errorMessage :  __('error_messages.system_error');
            return $this->apiResponse->getResponse($e->getCode(), null,  $errorResponseMessage, $errorFile, $errorLine);
        }
    }

    //verify mail-otp
    public function mailVerifyOtp(Request $request)
    {
        try {
            //check valid email validation
            $this->loginValidationService->checkEmailValidation($request->email);
            Log::info('Verify mail otp');
            //validate otp 
            $validateOtpResponse = $this->authLoginALRepository->checkMailOtp($request->email, $request->otp);
            if ($validateOtpResponse['status'] == 'validate') {
                //get user details using username/email/mobile
                $user = $this->authLoginALRepository->getUserByEmailOrUsername($request->email);
                if (empty($user)) {
                    $user = new AuthUser;
                    $userDetails = $this->authLoginALRepository->CreateMainTableEntry($request, $user);
                    if ($userDetails['status'] == 'success') {
                        $user = $userDetails['data'];
                        $customUserMessageTitle = __('messages.register_success_title');
                        $customUserMessageText = __('messages.register_success_text');
                        $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                    } else {
                        $customUserMessageTitle = __('error_messages.system_error');
                        $this->apiResponse->setCustomResponse($customUserMessageTitle);
                        throw new Exception($customUserMessageTitle, 500);
                    }
                }
                // return $user;
                $model_name = $user->getMorphClass();
                $userSettingDetails = AuthSetting::where('model_name', $model_name)->where('model_id', $user->id)->latest()->first();
                //check user if blocked
                if (config('al_auth_config.is_check_user_block') === true) {
                    if (!empty($userSettingDetails) && $userSettingDetails->user_status === 2) {
                        $customUserMessageTitle = __('error_messages.account_blocked_title');
                        $customUserMessageText = __('error_messages.account_blocked_text');
                        $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                        throw new Exception(__('error_messages.system_user_account_block'), 401);
                    }
                }
                $ability =  $userSettingDetails->user_type ? 'userType:' . $userSettingDetails->user_type : 'userType:' .  config('al_auth_config.user_Type');
                $apiToken = $this->tokenService->generateSanctumToken($user, $ability);
                $customUserMessageTitle = __('messages.otp_verify_success_title');
                $customUserMessageText = __('messages.otp_verify_success_text');
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

    //verify sms-otp
    public function smsVerifyOtp(Request $request)
    {
        try {
            //check syntax validation for mobile & country code
            $this->loginValidationService->checkMobileValidation($request);
            Log::info('Verify otp');
            //validate otp 
            $validateOtpResponse = $this->authLoginALRepository->checkSmsOtp($request->mobile, $request->country_code, $request->otp);
            if ($validateOtpResponse['status'] == 'validate') {
                //get user details using username/email/mobile
                $user = $this->authLoginALRepository->getUserByMobile($request->mobile, $request->country_code);
                if (empty($user)) {
                    $user = new AuthUser;
                    $userDetails = $this->authLoginALRepository->CreateMainTableEntry($request, $user);
                    if ($userDetails['status'] == 'success') {
                        $user = $userDetails['data'];
                        $customUserMessageTitle = __('messages.register_success_title');
                        $customUserMessageText = __('messages.register_success_text');
                        $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                    } else {
                        $customUserMessageTitle = __('error_messages.system_error');
                        $this->apiResponse->setCustomResponse($customUserMessageTitle);
                        throw new Exception($customUserMessageTitle, 500);
                    }
                }
                // return $user;
                $model_name = $user->getMorphClass();
                $userSettingDetails = AuthSetting::where('model_name', $model_name)->where('model_id', $user->id)->latest()->first();
                //check user if blocked
                if (config('al_auth_config.is_check_user_block') === true) {
                    if (!empty($userSettingDetails) && $userSettingDetails->user_status === 2) {
                        $customUserMessageTitle = __('error_messages.account_blocked_title');
                        $customUserMessageText = __('error_messages.account_blocked_text');
                        $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                        throw new Exception(__('error_messages.system_user_account_block'), 401);
                    }
                }
                $ability =  $userSettingDetails->user_type ? 'userType:' . $userSettingDetails->user_type : 'userType:' .  config('al_auth_config.user_Type');
                $apiToken = $this->tokenService->generateSanctumToken($user, $ability);
                $customUserMessageTitle = __('messages.otp_verify_success_title');
                $customUserMessageText = __('messages.otp_verify_success_text');
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

    //logout
    public function logout()
    {
        try {
            $details = $this->authLoginALRepository->logout();
            if ($details['status'] == 'success') {
                $customUserMessageTitle = __('messages.logout_success_title');
                $customUserMessageText = __('messages.logout_success_text');
                $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
            }
            return $this->apiResponse->getResponse(200, array());
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $errorLine = $e->getLine();
            $errorFile = $e->getFile();
            $errorResponseMessage = $errorMessage != null ? $errorMessage :  __('error_messages.system_error');
            return $this->apiResponse->getResponse($e->getCode(), null,  $errorResponseMessage, $errorFile, $errorLine);
        }
    }
    //update user setting
    public function userSettingUpdate(Request $request)
    {
        try {
            $user = Auth::user();
            $details =   $this->authLoginALRepository->updateAuthUserSetting($user, $request);

            if ($details['status'] == 'success') {
                $customUserMessageTitle = __('messages.user_setting_update_success_title');
                $customUserMessageText = __('messages.user_setting_update_success_text');
                $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
            }
            return $this->apiResponse->getResponse(200, array());
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $errorLine = $e->getLine();
            $errorFile = $e->getFile();
            $errorResponseMessage = $errorMessage != null ? $errorMessage :  __('error_messages.system_error');
            return $this->apiResponse->getResponse($e->getCode(), null,  $errorResponseMessage, $errorFile, $errorLine);
        }
    }


    //web email verification
    public function webEmailVerification($token)
    {
        try {
            $emailVerified = $this->authLoginALRepository->webEmailVerification($token);
            if ($emailVerified == 1) {
                return view('mails.user-email-verified')->with(['message' => __('messages.mail_success_email_verification')]);
            } else
                return view('mails.user-email-verified');
        } catch (\Exception $error) {
            return view('mails.user-email-verified');
        }
    }
    //SSO login using google/apple
    public function loginWithSso(Request $request)
    {
        try {
            $email = $request->input('email');
            $sso_type = $request->input('sso_type');
            $idToken = $request->input('idToken');
            $aud = $request->input('aud');
            //validate user email with id token
            $tokenCheck = $this->tokenService->checkTokenValidation($idToken, $aud, $sso_type, $email);

            $data = array();
            if ($tokenCheck['status'] === true) {
                $user = $this->authLoginALRepository->getUserByEmailOrUsername($email);
                if (empty($user)) {
                    $request['status'] = 'verified';
                    $request['email_verified_at'] = Carbon::now();
                    //update registration temporary table 
                    $tempUser = $this->authRegistrationALRepository->register($request);
                    $user = new AuthUser;
                    $userDetails = $this->authLoginALRepository->CreateMainTableEntry($request, $user);
                    if (!$userDetails['status'] == 'success') {
                        $customUserMessageTitle = __('error_messages.system_error');
                        $customUserMessageText = __('error_messages.system_error');
                        $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                        throw new Exception($customUserMessageTitle, 500);
                    }
                    $user = $userDetails['data'];
                }
                $ability = 'userType:' . config('al_auth_config.user_Type');
                $apiToken = $this->tokenService->generateSanctumToken($user, $ability);
                $customUserMessageTitle = __('messages.login_success_title');
                $customUserMessageText = __('messages.login_success_text');
                $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                $data = [
                    'accessToken' => $apiToken,
                    'user' => $user
                ];
            } else {
                $customUserMessageTitle = __('error_messages.invalid_token_title');
                $customUserMessageText = __('error_messages.invalid_token_text');
                $errorMessage = !empty($tokenCheck['errorMessage']) ? $tokenCheck['errorMessage'] : __('error_messages.invalid_token_text');
                $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                return $this->apiResponse->getResponse(401, null, $errorMessage);
            }
            return $this->apiResponse->getResponse(200, $data);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $errorLine = $e->getLine();
            $errorFile = $e->getFile();
            $errorResponseMessage = $errorMessage != null ? $errorMessage :  __('error_messages.system_error');
            return $this->apiResponse->getResponse($e->getCode(), null,  $errorResponseMessage, $errorFile, $errorLine);
        }
    }


    //User sent forgot password link
    public function sendForgotPasswordLink(Request $request)
    {
        try {
            //check valid email validation
            $this->loginValidationService->checkEmailValidation($request->email);
            //check user exist
            $user = $this->authLoginALRepository->getUserByEmailOrUsername($request->email);

            $customUserMessageTitle = __('messages.forgot_password_title');
            $customUserMessageText = __('messages.forgot_password_text');
            if (!empty($user)) {
                if (!empty($user->settings) && $user->settings->user_status == 2) {
                    $customUserMessageTitle = __('error_messages.forgot_password_blocked_user_title');
                    $customUserMessageText = __('error_messages.forgot_password_blocked_user_text');
                } else {
                    $this->userService->SendResetPasswordLinkService($user);
                }
            }
            $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
            return $this->apiResponse->getResponse(200);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $errorLine = $e->getLine();
            $errorFile = $e->getFile();
            $errorResponseMessage = $errorMessage != null ? $errorMessage :  __('error_messages.system_error');
            return $this->apiResponse->getResponse($e->getCode(), null,  $errorResponseMessage, $errorFile, $errorLine);
        }
    }

    //web email verification
    public function webResetPassword($token)
    {
        $details = $this->authLoginALRepository->webResetPasswordTokenValidate($token);
        if ($details['isTokenValidate'] == true) {
            $type = 'web';
            $view = view('blades.reset-password', compact('token', 'type'))->with(['isToken' => 'Token verified!']);
        } else {
            $view = view('blades.reset-password');
        }

        return $view;
    }

    public function webUpdatePassword(Request $request)
    {
        try {

            $data = $this->authLoginALRepository->webResetPasswordTokenValidate($request->user_token);
            if ($data['isTokenValidate'] == true) {

                $validate =   $this->loginValidationService->validateSetChangePassword($request);
                if (!empty($validate)) {
                    return Redirect::back()->withInput()->withErrors($validate);
                }

                $userPasswordUpdate = AuthUser::where('email', $data['userDetails']->email)->update([
                    'password' => Hash::make($request->password)
                ]);
                if ($userPasswordUpdate) {
                    $deleteEntry =  PasswordReset::where(['email' =>  $data['userDetails']->email])->delete();
                    return Redirect::back()->withInput()->with(['statusSuccess' => __('messages.mail_success_email_verification')]);
                } else
                    return back()->withInput()->with(['error' => 'Something went wrong!Please try again']);
            }
            return Redirect::back()->with(['error' =>  __('error_messages.mail_invalid_token')]);
        } catch (\Exception $error) {
            return Redirect::back()->with(['error' =>  __('error_messages.mail_invalid_token')]);
        }
    }


    public function userSetOrChangePassword(Request $request)
    {
        try {
            $customUserMessageTitle = __('messages.set_password_title');
            $customUserMessageText = __('messages.set_password_text');
            $user = Auth::user();
            $validate =   $this->loginValidationService->validateSetChangePassword($request);
            if (!empty($validate)) {
                $this->apiResponse->setCustomErrors($validate);
                throw new Exception('Validation error', 422);
            }
            if (empty($user)) {
                throw new Exception(__('messages.system_user_account_missing'), 401);
            } else if (!empty($user->password)) {
                $check = Hash::check($request->current_password, auth()->user()->password);
                if (empty($check)) {
                    $error = array("current_password" => [
                        config('al_auth_validation_config.validation_messages.current_password_invalid')
                    ]);
                    $this->apiResponse->setCustomErrors($error);
                    throw new Exception('Validation error', 422);
                } else {
                    $customUserMessageTitle = __('messages.change_password_title');
                    $customUserMessageText = __('messages.change_password_text');
                }
            }
            $userUpdate = AuthUser::where('id', $user->id)->update([
                'password' => Hash::make($request->password)
            ]);
            if (isset($userUpdate)) {
                $this->apiResponse->setCustomResponse($customUserMessageTitle, $customUserMessageText);
                return $this->apiResponse->getResponse(200, array());
            } else {
                throw new Exception(__('error_messages.system_error'), 500);
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
