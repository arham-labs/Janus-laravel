<?php

namespace Arhamlabs\Authentication\Services;

use Illuminate\Support\Facades\Validator;


class RegistrationValidationService
{
    //validate email/username
    public function validation($request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => config("al_auth_validation_config.validation_rules.registration_first_name"),
                'last_name' => config("al_auth_validation_config.validation_rules.registration_last_name"),
                'username' => config("al_auth_validation_config.validation_rules.registration_username"),
                'email' => config("al_auth_validation_config.validation_rules.registration_email"),
                'password' => config("al_auth_validation_config.validation_rules.registration_password"),
                'mobile' => config("al_auth_validation_config.validation_rules.registration_mobile"),
                'country_code' => config("al_auth_validation_config.validation_rules.registration_country_code"),
            ],
            [
                "first_name" => config("al_auth_validation_config.validation_messages.registration_first_name"),
                "last_name" => config("al_auth_validation_config.validation_messages.registration_last_name"),
                "username" => config("al_auth_validation_config.validation_messages.registration_username"),
                "email.required" => config("al_auth_validation_config.validation_messages.registration_email_required"),
                "email.unique" => config("al_auth_validation_config.validation_messages.registration_email_unique"),
                "email" => config("al_auth_validation_config.validation_messages.registration_email_invalid"),
                "password.required" => config("al_auth_validation_config.validation_messages.registration_password_required"),
                "password" => config("al_auth_validation_config.validation_messages.registration_password_invalid"),
                "mobile.required" => config("al_auth_validation_config.validation_messages.registration_mobile_required"),
                "mobile.unique" => config("al_auth_validation_config.validation_messages.registration_mobile_unique"),
                "mobile" => config("al_auth_validation_config.validation_messages.registration_mobile_invalid"),
                "country_code" => config("al_auth_validation_config.validation_messages.registration_country_code_invalid"),
            ]
        );
        
        if ($validator->fails()) {
            return $validator->errors();
        }
    }
    
     //validate otp mobile on registration process
     public function checkRegistrationMobileValidation($request)
     {
         $validator = Validator::make(
             ['mobile' => $request->mobile, 'country_code' => $request->country_code],
             [
                'mobile' => config("al_auth_validation_config.validation_rules.registration_mobile"),
                'country_code' => config("al_auth_validation_config.validation_rules.registration_country_code"),
             ],
             [
                 "mobile.required" => config("al_auth_validation_config.validation_messages.check_mobile_required"),
                 "mobile.regex" => config("al_auth_validation_config.validation_messages.check_mobile_regex"),
                 "mobile" => config("al_auth_validation_config.validation_messages.check_mobile_invalid"),
                 "mobile.required" => config("al_auth_validation_config.validation_messages.registration_mobile_required"),
                 "mobile.unique" => config("al_auth_validation_config.validation_messages.registration_mobile_unique"),
                 "mobile" => config("al_auth_validation_config.validation_messages.registration_mobile_invalid"),
                 "country_code" => config("al_auth_validation_config.validation_messages.registration_country_code_invalid"),
             ]
         );
 
         if ($validator->fails()) {
            return $validator->errors();
         }
     }
}
