<?php
return [

    //validation rules 
    'validation_rules' => [
        'registration_first_name' => '',

        'registration_last_name' => '',

        'registration_username' => '',

        "registration_email" => "required|email|unique:users,email",

        "registration_password" => 'required|min:6|max:15',

        "check_email" =>  "required|email",

        "check_username" => 'bail|required|regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9]+$/',

        "check_password" => 'required|min:6|max:15',

        "check_mobile" => 'required|regex:/^[0-9]{6,14}$/',

        "check_country_code" => ''
    ],

    'validation_messages' => [

        'registration_first_name' => 'First name required',

        'registration_last_name' => 'Last name required',

        'registration_username' => 'Username required',

        'registration_email_required' => 'Email is required',

        'registration_email_unique' => 'Email already exist',

        'registration_email_invalid' => 'Email format is invalid',

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

        'check_country_code_invalid' => 'Country code format is invalid'
    ]

];
