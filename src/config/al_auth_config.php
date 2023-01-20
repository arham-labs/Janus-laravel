<?php
return [
    //check email verification requirement
    'email_required' => true,

    //check to send email verification mail
    'email_verification_send' => true,

    //check mobile verification requirement 
    'mobile_verification_require' => true,

    //length for otp 
    'otp_length' => 6,


    //otp expire in minutes
    'otp_expire' => 5,

    //allow multi login with same credentials
    'user_multi_login' => false,

    //allow temp registration
    // 'temp_registration' => true,

    //token expiry(platform dependency) in hours
    'token_expiry' => 48,


    //default user type
    'user_Type' => 'app_user',


    //default user type
    'user_status' => [
        'active' => 1,
        'blocked' => 2,
        'deleted' => 3,
    ],

    //email verification mail expiry(platform dependency) in hours
    'email_verification_mail_expiry' => 48
];
