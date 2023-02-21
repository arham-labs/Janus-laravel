<?php

use Arhamlabs\Authentication\Http\Controllers\AuthLoginALController;
use Arhamlabs\Authentication\Http\Controllers\AuthRegistrationALController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {

    //User registration using email,password
    Route::post('api/package/auth/register', [AuthRegistrationALController::class, 'registerUser']);

    //User login using email,password
    Route::post('api/package/auth/login', [AuthLoginALController::class, 'login']);


    //User login using email,password
    Route::post('api/package/auth/sso-login', [AuthLoginALController::class, 'loginWithSso']);

    //User login using mobile,otp
    Route::post('api/package/auth/sent-mobile-otp', [AuthLoginALController::class, 'sentMobileOtp']);

    //User login using mobile,otp
    Route::post('api/package/auth/sent-email-otp', [AuthLoginALController::class, 'sentEmailOtp']);

    //User login using mobile,otp
    Route::post('api/package/auth/verify-otp', [AuthLoginALController::class, 'verifyOtp']);
});

// Token Access
Route::middleware('auth:sanctum', 'abilities:userType:app_user')->group(function () {
    //User logout
    Route::post('api/package/auth/logout', [AuthLoginALController::class, 'logout']);

    //Update user setting
    Route::post('api/package/auth/setting/update', [AuthLoginALController::class, 'userSettingUpdate']);
});
