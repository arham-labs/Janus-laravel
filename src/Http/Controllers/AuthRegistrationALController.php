<?php

namespace Arhamlabs\Authentication\Http\Controllers;

use Arhamlabs\ApiResponse\ApiResponse;
use Arhamlabs\Authentication\Interfaces\AuthLoginALInterface;
use Arhamlabs\Authentication\Interfaces\AuthRegistrationALInterface;
use Arhamlabs\Authentication\Models\AuthUser;
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
            //    return $validate;
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
                    $user = new AuthUser;
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
}
