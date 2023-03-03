<?php

namespace Arhamlabs\Authentication\Services;

use Arhamlabs\ApiResponse\ApiResponse;
use Arhamlabs\Authentication\Jobs\SendOtpJob;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class LoginValidationService
{
    private $apiResponse;
    public function __construct(
        ApiResponse $apiResponse,
    ) {
        $this->apiResponse = $apiResponse;
    }
    //validate email/username
    public function validation($request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'string',
                'last_name' => 'string',
                'username' => 'string',
                'email' => 'email|unique:users,email',
                'password' => 'min:6|max:6',
            ],
            [
                "email.required" => __("validation_messages.email_required"),
                "email.unique" => __("validation_messages.email_unique"),
                "email" => __("validation_messages.email_invalid"),
                "password" => __("validation_messages.password_invalid"),
            ]
        );
        if ($validator->fails()) {
            return $validator->errors();
        }
        return array();
    }

    //validate email/username
    public function checkEmailOrUsername($email)
    {
        if (!empty($email) && str_contains($email, '@')) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validator = Validator::make(
                    ['email' => $email],
                    [
                        'email' => 'bail|required|email',
                    ],
                    [
                        "email.required" => __("validation_messages.email_required"),
                        "email" => __("validation_messages.email_invalid"),
                    ]
                );

                if ($validator->fails()) {
                    Log::info('Validation failed for email ', (array)$validator->errors()->first());
                    throw new Exception($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            } else {
                Log::info('Email address is not valid');
                throw new Exception(__("validation_messages.email_invalid"), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            //This regular expression is used to validate a string that contains only alphanumeric characters and at least one letter (uppercase or lowercase).
            $validator = Validator::make(
                ['username' => $email],
                [
                    'username' => 'bail|required|regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9]+$/',
                ],
                [
                    "username.required" => __("validation_messages.username_required"),
                    "username.regex" => __("validation_messages.username_invalid"),
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
                'password' => 'required|min:6|max:12'
            ],
            [
                "password.required" => __("validation_messages.password_required"),
                "password" => __("validation_messages.password_invalid"),
            ]
        );

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    //validate mobile
    public function checkMobileValidation($request)
    {
        $validator = Validator::make(['mobile' => $request->mobile, 'country_code' => $request->country_code], [
            'mobile' => 'required|regex:/^[0-9]{6,14}$/',
            'country_code' => 'required'
        ]);

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
                'email' => 'bail|required|email',
            ],
            [
                "email.required" => __("validation_messages.email_required"),
                "email" => __("validation_messages.email_invalid"),
            ]
        );

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
