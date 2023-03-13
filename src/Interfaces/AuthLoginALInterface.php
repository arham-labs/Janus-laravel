<?php

namespace Arhamlabs\Authentication\Interfaces;

interface AuthLoginALInterface
{

    //User register using mobile,password
    public function sentMobileOtp($request);

    //User register using mobile,password
    public function CreateMainTableEntry($request, $user);

    //get user details by username|email|mobile
    public function getUserByEMU($username);

    //User details using mobile
    public function getUserByMobile($mobile);

    //User details using email|username
    public function getUserByEmailOrUsername($email);

    //Sent OTP via mail
    public function sentEmailOtp($user);

    //verify OTP
    public function checkOtp($user_id, $otp);

    //User logout
    public function logout();

    //Create|update auth user setting
    public function updateAuthUserSetting($user, $request);

    // User web email verification
    public function webEmailVerification($request);

    // User token validation for reset password
    public function webResetPasswordTokenValidate($request);
}
