<?php

namespace Arhamlabs\Authentication\Services;

use Google_Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


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

        if ($sso_type == 'linkedin-mobile') {
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
        
        if ($sso_type == 'linkedin-web') {
            $valid["status"] = false;
            $client_id = config('al_auth_config.linkedin.LINKEDIN_CLIENT_ID');
            $client_secret = config('al_auth_config.linkedin.LINKEDIN_CLIENT_SECRET');
            $redirect_uri = config('al_auth_config.linkedin.LINKEDIN_REDIRECT_URI');
            $CURLOPT_SSL_VERIFYPEER = config('al_auth_config.linkedin.CURLOPT_SSL_VERIFYPEER');

            // return "grant_type:authorization_code&code=$idToken,redirect_uri=$redirect_uri&client_id=$client_id&client_secret=$client_secret";
            $ch = curl_init('https://www.linkedin.com/oauth/v2/accessToken');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=$idToken&redirect_uri=$redirect_uri&client_id=$client_id&client_secret=$client_secret");


            // Disable SSL verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $CURLOPT_SSL_VERIFYPEER);
            // execute!
            $response = curl_exec($ch);
            // close the connection, release resources used
            curl_close($ch);
            // $response contains

            if ($response === false) {
                $error = curl_error($ch);
                // Handle the error
                $valid["status"] = false;
                $valid["message"] = __('error_messages.invalid_token_title');
                $valid["errorMessage"] = $error;
                Log::error("Linkedin web error:");
                Log::error($error);
            } else {
                $result = json_decode($response, true);
                if ($result === null) {
                    // Failed to decode JSON response
                    $valid["status"] = false;
                    $valid["message"] = __('error_messages.invalid_token_title');
                    $valid["errorMessage"] = 'Failed to decode JSON response';
                    Log::error("Linkedin web error:");
                    Log::error('Failed to decode JSON response');
                } else {

                    if (!empty($result) && !empty($result['access_token'])) {
                        // Set the API endpoint URL for retrieving user profile details
                        $profileUrl = 'https://api.linkedin.com/v2/me?projection=(id,firstName,lastName)';

                        // Set the API endpoint URL for retrieving email address
                        $emailUrl = 'https://api.linkedin.com/v2/clientAwareMemberHandles?q=members&projection=(elements*(primary,type,handle~))';

                        // Set the headers including the access token
                        $headers = [
                            'Authorization: Bearer ' . $result['access_token'],
                            'Connection: Keep-Alive',
                            'Accept: application/json',
                        ];

                        // Initialize cURL for retrieving user profile details
                        $ch = curl_init($profileUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        // Disable SSL verification
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $CURLOPT_SSL_VERIFYPEER);

                        // Execute the request to retrieve user profile details
                        $profileResponse = curl_exec($ch);
                        if ($profileResponse === false) {
                            $error = curl_error($ch);
                            $errorCode = curl_errno($ch);
                            // Handle the error
                            $valid["status"] = false;
                            $valid["message"] = __('error_messages.invalid_token_title');
                            $valid["errorMessage"] = $error;
                            Log::error("Linkedin web error:");
                            Log::error($error);
                        } else {
                            // Decode the response JSON for user profile details
                            $profileResult = json_decode($profileResponse, true);
                            if ($profileResult === null) {
                                // Failed to decode JSON response
                                $valid["status"] = false;
                                $valid["message"] = __('error_messages.invalid_token_title');
                                $valid["errorMessage"] = 'Failed to decode profile response JSON response';
                                Log::error("Linkedin web error:");
                                Log::error('Failed to decode email response JSON response');
                            } else {
                                // Initialize cURL for retrieving email address
                                $ch = curl_init($emailUrl);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                // Disable SSL verification
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $CURLOPT_SSL_VERIFYPEER);

                                // Execute the request to retrieve email address
                                $emailResponse = curl_exec($ch);

                                if ($emailResponse === false) {
                                    $error = curl_error($ch);
                                    $errorCode = curl_errno($ch);
                                    // Handle the error
                                    $valid["status"] = false;
                                    $valid["message"] = __('error_messages.invalid_token_title');
                                    $valid["errorMessage"] = $error;
                                    Log::error("Linkedin web error:");
                                    Log::error($error);
                                } else {
                                    // Decode the response JSON for email address
                                    $emailResult = json_decode($emailResponse, true);
                                    if ($emailResult === null) {
                                        // Failed to decode JSON response
                                        $valid["status"] = false;
                                        $valid["message"] = __('error_messages.invalid_token_title');
                                        $valid["errorMessage"] = 'Failed to decode email response JSON response';
                                        Log::error("Linkedin web error:");
                                        Log::error('Failed to decode email response JSON response');
                                    } else {
                                        if (!empty($profileResult['firstName']['localized']['en_US']) && !empty($profileResult['lastName']['localized']['en_US']) && !empty($emailResult['elements'][0]['handle~']['emailAddress'])) {
                                            $valid["status"] = true;
                                            // Retrieve the user details from the profileResult array
                                            $valid["first_name"] = $profileResult['firstName']['localized']['en_US'];
                                            $valid["last_name"] = $profileResult['lastName']['localized']['en_US'];
                                            // Retrieve the email address from the emailResult array
                                            $valid["email"] = $emailResult['elements'][0]['handle~']['emailAddress'];                                            
                                            Log::info('SSO linkedin web login success');
                                        } else {
                                            $valid["status"] = false;
                                            $valid["message"] = __('error_messages.invalid_token_title');
                                            $valid["errorMessage"] = 'Failed to get personal deatils';
                                            Log::error("Linkedin web error:");
                                            Log::error($profileResult);
                                            Log::error($emailResult);
                                            Log::error('Failed to get personal deatils');
                                        }
                                    }
                                }
                            }
                            // Close the cURL connection
                            curl_close($ch);
                        }
                    }
                }
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
