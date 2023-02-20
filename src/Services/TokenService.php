<?php

namespace Arhamlabs\Authentication\Services;

use Exception;
use Google_Client;


class TokenService
{
    public function  checkTokenValidation($idToken, $aud, $ssoType, $email)
    {
        $valid = true;
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
