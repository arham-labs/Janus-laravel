<?php

namespace Arhamlabs\Authentication\Repositories;

use Arhamlabs\Authentication\Models\TempOtp;
use App\Models\User;
use Arhamlabs\Authentication\Interfaces\AuthRegistrationALInterface;
use Arhamlabs\Authentication\Jobs\SendOtpJob;
use Arhamlabs\Authentication\Models\AuthSetting;
use Arhamlabs\Authentication\Models\AuthUser;
use Arhamlabs\Authentication\Models\TempRegistration;
use Arhamlabs\Authentication\Request\AuthRegistrationRequest;
use Arhamlabs\Authentication\Services\UserService;
use Arhamlabs\Authentication\Services\TokenService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthRegistrationALRepository implements AuthRegistrationALInterface
{
    public $userService;
    public $authLoginALRepository;
    public $tokenService;
    public function __construct(
        UserService $userService,
        TokenService $tokenService,
        AuthLoginALRepository $authLoginALRepository
    ) {
        $this->userService = $userService;
        $this->tokenService = $tokenService;
        $this->authLoginALRepository = $authLoginALRepository;
    }

    //temporary registration 
    public function register($request)
    {
        // try {
        $tempUserCreated = TempRegistration::create([
            'uuid' => Str::uuid(),
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'username' => $request->username,
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'mobile' => $request->mobile,
            'status' => $request->status ? $request->status : 'pending'
        ]);

        if (isset($tempUserCreated)) {
            return [
                'status' => 'success',
                "data" => $tempUserCreated
            ];
        } else {
            return [
                'status' => 'error',
                "data" => null
            ];
        }
        // } catch (Exception $e) {
        //     $errorMessage = $e->getMessage();
        //     $errorResponseMessage = $errorMessage != null ? $errorMessage : __('error_messages.system_error');
        //     throw new Exception($errorResponseMessage, $e->getCode());
        // }
    }
}