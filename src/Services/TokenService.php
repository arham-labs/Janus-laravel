<?php

namespace Arhamlabs\Authentication\Services;

use Exception;
use Google_Client;


class TokenService
{
    public function  checkTokenValidation($idToken, $aud, $ssoType, $email)
    {
        $valid = false;
        if ($ssoType == 'google') {
            $valid = false;
            $client = new Google_Client(['client_id' => $aud]);
            $payload = $client->verifyIdToken($idToken);
            if ($payload) {
                if (isset($payload['email']) && $payload['email'] = $email) {
                    $valid = true;
                }
            }
        }

        if ($ssoType == 'linkedin') {
            $valid = false;
            // $grant_type = urlencode('authorization_code');
            $idToken = $idToken;
            // $redirect_uri = urlencode(config('al_auth_config.linkedin.LINKEDIN_REDIRECT_URI'));
            $client_id = config('al_auth_config.linkedin.LINKEDIN_CLIENT_ID');
            $client_secret = config('al_auth_config.linkedin.LINKEDIN_CLIENT_SECRET');
            $ch = curl_init('https://www.linkedin.com/oauth/v2/introspectToken');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, "token=$idToken&client_id=$client_id&client_secret=$client_secret");
            // execute!
            $response = curl_exec($ch);
            // close the connection, release resources used
            curl_close($ch);
            // $response contains
            $result = json_decode($response);
            // dd($result);
            if (isset($result) && $result->active === true) {
                $valid = true;
            }
        }
        return $valid;
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
