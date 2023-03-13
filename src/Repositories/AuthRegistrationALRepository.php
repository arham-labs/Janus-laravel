<?php

namespace Arhamlabs\Authentication\Repositories;

use Arhamlabs\Authentication\Interfaces\AuthRegistrationALInterface;
use Arhamlabs\Authentication\Models\TempRegistration;
use Arhamlabs\Authentication\Services\UserService;
use Arhamlabs\Authentication\Services\TokenService;
use Illuminate\Support\Facades\Hash;
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
        $password = $request->password;
        if (isset($request->password)) {
            if (Hash::needsRehash($password)) {
                $password = Hash::make($password);
            }
        }
        $tempUserCreated = TempRegistration::create([
            'uuid' => Str::uuid(),
            'password' => $password ? $password : null,
            'email' => $request->email,
            'sso_type' => $request->sso_type,
            'user_type' => $request->user_type,
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,
            'country_code' => $request->country_code,
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
    }
}
