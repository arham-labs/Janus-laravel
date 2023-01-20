<?php

namespace Arhamlabs\Authentication\Services;

use Exception;


class TokenService
{
    public function  checkTokenValidation($token, $ssoType, $email)
    {
        try {
            return true;
            $valid = false;
            if ($ssoType == 'google') {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://oauth2.googleapis.com/tokeninfo?id_token=$token",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $result = json_decode($response);
                if (isset($result->error)) {
                    throw new Exception($result->error, 401);
                } else if (isset($result->email_verified)) {
                    if (isset($result->email) && $result->email = $email) {
                        $valid = true;
                    }
                }
            }
            return $valid;
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $errorResponseMessage = $errorMessage != null ? $errorMessage :  __('error_messages.system_error');
            throw new Exception($errorResponseMessage, $e->getCode());
        }
    }
    public function generateSanctumToken($user, $ability)
    {
        if (config('al_auth_config.user_multi_login') === false)
            $user->tokens()->delete();
        return   $accessToken = $user->createToken('accessToken', [$ability])->plainTextToken;
    }
    public function deleteSanctumToken($user)
    {
        if (config('al_auth_config.user_multi_login') === true)
            $user->currentAccessToken()->delete();
        else
            $user->tokens()->delete();
        return true;
    }
}
