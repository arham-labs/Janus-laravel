<?php

namespace Arhamlabs\Authentication\Services;

use Arhamlabs\ApiResponse\ApiResponse;
use Arhamlabs\Authentication\Jobs\SendOtpJob;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class RegistrationValidationService
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
        $validator = Validator::make($request->all(), [
            'firstName' => 'string',
            'lastName' => 'string',
            'username' => 'string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:15',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return array();
    }
}
