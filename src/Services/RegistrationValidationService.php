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
            ],
            [
                "first_name" => config("al_auth_validation_config.validation_messages.registration_first_name"),
                "last_name" => config("al_auth_validation_config.validation_messages.registration_last_name"),
                "username" => config("al_auth_validation_config.validation_messages.registration_username"),
                "email.required" => config("al_auth_validation_config.validation_messages.registration_email_required"),
                "email.unique" => config("al_auth_validation_config.validation_messages.registration_email_unique"),
                "email" => config("al_auth_validation_config.validation_messages.registration_email_invalid"),
                "password.required" => config("al_auth_validation_config.validation_messages.registration_password_required"),
                "password" => config("al_auth_validation_config.validation_messages.registration_password_invalid")
            ]
        );
        if ($validator->fails()) {
            return $validator->errors();
        }
    }
}
