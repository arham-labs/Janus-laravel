<?php
return [

    //validation rules 
    'validation_rules' => [
        'registration_first_name' => '',

        'registration_last_name' => '',

        'registration_username' => '',

        "registration_email" => "email|unique:users,email",

        "registration_mobile" => "unique:users,mobile|regex:/^[0-9]{6,14}$/",

        "registration_country_code" =>  "required",

        "registration_password" => '',

        "check_email" =>  "required|email",

        "check_username" => 'bail|required|regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9]+$/',

        "check_password" => 'required|min:6|max:15',

        "check_mobile" => 'required|regex:/^[0-9]{6,14}$/',

        "check_country_code" => '',

        "web_check_password" => 'bail|required|min:6|max:15|confirmed',

        "web_check_confirm_password" => 'required',
    ],

    'validation_messages' => [

        'registration_first_name' => 'First name required',

        'registration_last_name' => 'Last name required',

        'registration_username' => 'Username required',

        'registration_email_required' => 'Email is required',

        'registration_email_unique' => 'Email already exist',

        'registration_email_invalid' => 'Email format is invalid',

        'registration_mobile_required' => 'Mobile is required',

        'registration_mobile_unique' => 'Mobile already exist',

        'registration_mobile_invalid' => 'Mobile format is invalid',

        'registration_country_code_invalid' => 'Country code is required',

        'registration_password_required' => 'Password required',

        'registration_password_invalid' => 'Password format is invalid',

        'registration_username_invalid' => 'Username required',

        'registration_username_required' => 'Username format is invalid',

        'check_email_required' => 'Email required',

        'check_email_invalid' =>  'Email format is invalid',

        'check_username_required' =>  'Username required',

        'check_username_regex' =>  'Username format is invalid',

        'check_username_invalid' =>  'Username format is invalid',

        'check_password_required' => 'Password required',

        'check_password_regex' => 'Password format is invalid',

        'check_password_invalid' => 'Password format is invalid',

        'check_mobile_required' => 'mobile required',

        'check_mobile_regex' => 'mobile format is invalid',

        'check_mobile_invalid' => 'mobile format is invalid',

        'check_country_code_required' => 'Country code required',

        'check_country_code_regex' => 'Country code format is invalid',

        'check_country_code_invalid' => 'Country code format is invalid',

        'web_check_password_required' => 'Password required',

        'web_check_password_regex' => 'Password format is invalid',

        'web_check_password_invalid' => 'Password format is invalid',

        'web_check_confirm_password_required' => 'Confirm Password required',

        'web_check_confirm_password_invalid' => 'The new password and confirm password do not match',

        'current_password_invalid' => 'Current password is invalid',
    ]

];
