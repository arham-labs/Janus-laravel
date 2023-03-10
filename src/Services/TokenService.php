<?php

namespace Arhamlabs\Authentication\Services;

use Google_Client;
use Carbon\Carbon;


class TokenService
{

    public function  checkTokenValidation($idToken, $aud, $sso_type, $email)
    {
        $valid = array(
            'status' => false,
            "message" => __('error_messages.invalid_token_title'),
            "errorMessage" => __('error_messages.invalid_token_title')
        );
        if ($sso_type == 'google') {
            $valid["status"] = false;
            $client = new Google_Client(['client_id' => $aud]);
            $payload = $client->verifyIdToken($idToken);
            if ($payload) {
                if (isset($payload['email']) && $payload['email'] = $email) {
                    $valid["status"] = true;
                    $valid["message"] = "Token validate successfully";
                }
            }
        }

        if ($sso_type == 'linkedin') {
            $valid["status"] = false;
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
            if (isset($result) && $result->active === true) {
                $valid["status"] = true;
                $valid["message"] = "Token validate successfully";
            }
        }

        if ($sso_type == 'apple') {
            $valid["status"] = false;
            // Decode the JWT token
            $jwtParts = explode('.', $idToken);
            $jwtHeader = json_decode(base64_decode($jwtParts[0]), true);
            $jwtPayload = json_decode(base64_decode($jwtParts[1]), true);
            $jwtSignature = base64_decode($jwtParts[2]);

            if (!empty($jwtPayload)) {
                $expiration = Carbon::createFromTimestamp($jwtPayload['exp']);
                $tokenExpired = (Carbon::now()->diffInSeconds($expiration, false) < 0);
                if ($tokenExpired) {
                    $valid["status"] = false;
                    $valid["message"] = __('error_messages.invalid_token_title');
                    $valid["errorMessage"] = "Token expired";
                } elseif (empty($jwtPayload['nonce'])) {
                    $valid["status"] = false;
                    $valid["message"] = __('error_messages.invalid_token_title');
                    $valid["errorMessage"] = "Nonce empty";
                } elseif (empty($jwtPayload['iss']) || $jwtPayload['iss'] != config("al_auth_config.apple.TOKEN_ISS")) {
                    $valid["status"] = false;
                    $valid["message"] = __('error_messages.invalid_token_title');
                    $valid["errorMessage"] = "invalid iss";
                } elseif (empty($jwtPayload['aud']) || $jwtPayload['aud'] != config("al_auth_config.apple.TOKEN_AUD")) {
                    $valid["status"] = false;
                    $valid["message"] = __('error_messages.invalid_token_title');
                    $valid["errorMessage"] = "invalid client id";
                } else {
                    //match public apple public keys 
                    $publicKeyUrl = 'https://appleid.apple.com/auth/keys';
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $publicKeyUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);
                    curl_close($ch);

                    $keys = json_decode($result, true);

                    $matchKey = false;
                    $matchePublicKey = null;
                    foreach ($keys['keys'] as $key) {
                        if ($key['kid'] == $jwtHeader['kid'] && $key['alg'] == $jwtHeader['alg']) {
                            $matchePublicKey = $key;
                            $matchKey = true;
                            break;
                        }
                    }

                    if ($matchKey == false) {
                        $valid["status"] = false;
                        $valid["message"] = __('error_messages.invalid_token_title');
                        $valid["errorMessage"] = "invalid apple public key";
                    } else {
                        $valid["status"] = true;
                        $valid["message"] = "Token validate successfully";
                    }
                }
            } else {
                $valid["status"] = false;
                $valid["message"] = __('error_messages.invalid_token_title');
                $valid["errorMessage"] = "Token payload missing";
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
