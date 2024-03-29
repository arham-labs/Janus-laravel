<?php

use Arhamlabs\Authentication\Http\Controllers\AuthLoginALController;
use Arhamlabs\Authentication\Http\Controllers\AuthRegistrationALController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {

    //User registration using email,password
    Route::post('api/package/auth/register', [AuthRegistrationALController::class, 'registerUser']);

    
    //User registration using mobile,country code
    Route::post('api/package/auth/mobile-register', [AuthRegistrationALController::class, 'registerMobileUser']);
    
    //Registration verify mobile,otp
    Route::post('api/package/auth/mobile-register-verify-otp', [AuthRegistrationALController::class, 'registrationMobileVerifyOtp']);

    //User login using email,password
    Route::post('api/package/auth/login', [AuthLoginALController::class, 'login']);


    //User login using email,password
    Route::post('api/package/auth/sso-login', [AuthLoginALController::class, 'loginWithSso']);

    //User login using mobile,otp
    Route::post('api/package/auth/sent-mobile-otp', [AuthLoginALController::class, 'sentMobileOtp']);

    //User login using mobile,otp
    Route::post('api/package/auth/sent-email-otp', [AuthLoginALController::class, 'sentEmailOtp']);

    //User login using mobile,otp
    Route::post('api/package/auth/mail-verify-otp', [AuthLoginALController::class, 'mailVerifyOtp']);

    //User login using mobile,otp
    Route::post('api/package/auth/sms-verify-otp', [AuthLoginALController::class, 'smsVerifyOtp']);

    //User forgot password via web
    Route::post('api/package/auth/forgot-password', [AuthLoginALController::class, 'sendForgotPasswordLink']);
});

// Token Access
Route::middleware('auth:sanctum', 'abilities:userType:app_user')->group(function () {
    //User logout
    Route::post('api/package/auth/logout', [AuthLoginALController::class, 'logout']);

    //Update user setting
    Route::post('api/package/auth/setting/update', [AuthLoginALController::class, 'userSettingUpdate']);


    //Set/Change password
    Route::post('api/package/auth/update-password', [AuthLoginALController::class, 'userSetOrChangePassword']);
});
