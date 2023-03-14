<?php

namespace Arhamlabs\Authentication\Services;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class LoginValidationService
{

    //validate email/username
    public function checkEmailOrUsername($email)
    {
        if (!empty($email) && str_contains($email, '@')) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validator = Validator::make(
                    ['email' => $email],
                    [
                        'email' => config("al_auth_validation_config.validation_rules.check_email"),
                    ],
                    [
                        "email.required" => config("al_auth_validation_config.validation_messages.check_email_required"),
                        "email" => config("al_auth_validation_config.validation_messages.check_email"),
                    ]
                );

                if ($validator->fails()) {
                    Log::info('Validation failed for email ', (array)$validator->errors()->first());
                    throw new Exception($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            } else {
                Log::info('Email address is not valid');
                throw new Exception(config("al_auth_validation_config.validation_messages.check_email"), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            $validator = Validator::make(
                ['username' => $email],
                [
                    'username' => config("al_auth_validation_config.validation_rules.check_username"),
                ],
                [
                    "username.required" => config("al_auth_validation_config.validation_messages.check_username_required"),
                    "username.regex" => config("al_auth_validation_config.validation_messages.check_username_regex"),
                    "username" => config("al_auth_validation_config.validation_messages.check_username_invalid"),
                ]
            );
            if ($validator->fails()) {
                Log::info('Validation failed for username ', (array)$validator->errors());
                throw new Exception($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
    }

    //validate email/username
    public function checkPassword($password)
    {
        $validator = Validator::make(
            ['password' => $password],
            [
                'password' => config("al_auth_validation_config.validation_rules.check_password"),
            ],
            [
                "password.required" => config("al_auth_validation_config.validation_messages.check_password_required"),
                "password.regex" => config("al_auth_validation_config.validation_messages.check_password_regex"),
                "password" => config("al_auth_validation_config.validation_messages.check_password_invalid")
            ]
        );

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    //validate mobile
    public function checkMobileValidation($request)
    {
        $validator = Validator::make(
            ['mobile' => $request->mobile, 'country_code' => $request->country_code],
            [
                'mobile' => config("al_auth_validation_config.validation_rules.check_mobile"),
                'country_code' => config("al_auth_validation_config.validation_rules.check_country_code")
            ],
            [
                "mobile.required" => config("al_auth_validation_config.validation_messages.check_mobile_required"),
                "mobile.regex" => config("al_auth_validation_config.validation_messages.check_mobile_regex"),
                "mobile" => config("al_auth_validation_config.validation_messages.check_mobile_invalid"),
                "country_code.required" => config("al_auth_validation_config.validation_messages.check_country_code_required"),
                "country_code.regex" => config("al_auth_validation_config.validation_messages.check_country_code_regex"),
                "country_code" => config("al_auth_validation_config.validation_messages.check_country_code_invalid")
            ]
        );

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    //validate email
    public function checkEmailValidation($email)
    {
        $validator = Validator::make(
            ['email' => $email],
            [
                'email' => config("al_auth_validation_config.validation_rules.check_email"),
            ],
            [
                "email.required" => config("al_auth_validation_config.validation_messages.check_email_required"),
                "email" => config("al_auth_validation_config.validation_messages.check_email"),
            ]
        );

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    //validate set/change password request
    public function validateSetChangePassword($request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'password' => config("al_auth_validation_config.validation_rules.web_check_password"),
                'password_confirmation' => config("al_auth_validation_config.validation_rules.web_check_confirm_password"),
            ],
            [
                "password.required" => config("al_auth_validation_config.validation_messages.web_check_password_required"),
                "password.regex" => config("al_auth_validation_config.validation_messages.web_check_password_regex"),
                "password.confirmed" => config("al_auth_validation_config.validation_messages.web_check_confirm_password_invalid"),
                "password" => config("al_auth_validation_config.validation_messages.web_check_password_invalid"),
                "password_confirmation.required" => config("al_auth_validation_config.validation_messages.web_check_confirm_password_required"),
                "password_confirmation" => config("al_auth_validation_config.validation_messages.web_check_password_invalid")
            ]

        );

        if ($validator->fails()) {
            return $validator->errors();
        }
    }
}
