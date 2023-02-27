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
    'user_multi_login' => true,

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
    'email_verification_mail_expiry' => 48,

    //email verification mail expiry(platform dependency) in hours
    'email_encryption_key' => env('EMAIL_ENCRYPTION_KEY', 'ALAUTH'),

    'linkedin' => [
        'LINKEDIN_REDIRECT_URI' => env('LINKEDIN_REDIRECT_URI'),
        'LINKEDIN_CLIENT_ID' => env('LINKEDIN_CLIENT_ID'),
        'LINKEDIN_CLIENT_SECRET' => env('LINKEDIN_CLIENT_SECRET')
    ],

    'apple' => [
        'TOKEN_ISS' => env('TOKEN_ISS', "https://appleid.apple.com"),
        'TOKEN_AUD' => env('TOKEN_AUD', "com.example.co.uk.app"),
    ]
];
