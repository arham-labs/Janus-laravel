<?php
return [
    //check email verification requirement
    'email_verification' => true,

    //length for otp 
    'otp_length' => 4,

    //otp expire in minutes
    'otp_expire' => 5,
    
    //SMS OTP Configuration
    'sms' => [
        'delay' => 10, //in seconds
        'per_day_count' => 5 //per day sms limit for user
    ],

    //allow multi login with same credentials
    'user_multi_login' => true,

    //default user type
    'user_Type' => 'app_user',

    //if true then it will check user block status
    'is_check_user_block' => true,

    //email verification mail expiry in hours
    'email_verification_mail_expiry' => 48,

    //forgot password mail expiry in hours
    'forgot_password_mail_expiry' => 48,

    //email link encryption key
    'email_encryption_key' => env('EMAIL_ENCRYPTION_KEY', 'ALAUTH'),

    //social media login linkedin config setup
    'linkedin' => [
        'LINKEDIN_REDIRECT_URI' => env('LINKEDIN_REDIRECT_URI'),
        'LINKEDIN_CLIENT_ID' => env('LINKEDIN_CLIENT_ID'),
        'LINKEDIN_CLIENT_SECRET' => env('LINKEDIN_CLIENT_SECRET')
    ],

    //social media login apple config setup
    'apple' => [
        'TOKEN_ISS' => env('TOKEN_ISS', "https://appleid.apple.com"),
        'TOKEN_AUD' => env('TOKEN_AUD', "com.example.co.uk.app"),
    ]
];
