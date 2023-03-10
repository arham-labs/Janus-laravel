<?php

namespace Arhamlabs\Authentication\Repositories;

use Arhamlabs\Authentication\Models\TempOtp;
use App\Models\User;
use Arhamlabs\Authentication\Interfaces\AuthLoginALInterface;
use Arhamlabs\Authentication\Jobs\SendOtpJob;
use Arhamlabs\Authentication\Models\AuthSetting;
use Arhamlabs\Authentication\Models\AuthUser;
use Arhamlabs\Authentication\Models\TempRegistration;
use Arhamlabs\Authentication\Request\AuthRegistrationRequest;
use Arhamlabs\Authentication\Services\UserService;
use Arhamlabs\Authentication\Services\TokenService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Arhamlabs\Authentication\Models\PasswordReset;
class AuthLoginALRepository implements AuthLoginALInterface
{
    public $userService;
    public $tokenService;
    public function __construct(
        UserService $userService,
        TokenService $tokenService
    ) {
        $this->userService = $userService;
        $this->tokenService = $tokenService;
    }

    //update main user table with
    public function CreateMainTableEntry($request, $model)
    {
        // try {

        $password = $request->password;
        if (isset($request->password)) {
            if (Hash::needsRehash($password)) {
                $password = Hash::make($password);
            }
        }
        $createRow = $model->create([
            'uuid' => Str::uuid(),
            'password' => $password ? $password : null,
            'email' => $request->email,
            'sso_type' => $request->sso_type,
            'user_type' => $request->user_type,
            'username' => $request->username,
            'name' => $request->first_name ? $request->first_name . ' ' . $request->last_name :  null,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,
            'country_code' => $request->country_code,
            'email_verified_at' => $request->email_verified_at ? $request->email_verified_at : null,
        ]);
        if (isset($createRow)) {
            // $model_name = $createRow->getMorphClass();
            $this->updateAuthUserSetting($createRow, $request);
            return [
                'status' => 'success',
                "data" => $createRow,
            ];
        } else {
            return [
                'status' => 'error',
                "data" => null
            ];
        }
        // } catch (Exception $e) {
        //     $errorMessage = $e->getMessage();
        //     $errorResponseMessage = $errorMessage != null ? $errorMessage :  __('error_messages.system_error');
        //     throw new Exception($errorResponseMessage, $e->getCode());
        // }
    }

    //send otp via sms on mobile
    public function sentMobileOtp($request)
    {
        // try {
        $expireTime = config('al_config.otp_expire') ? config('al_config.otp_expire') : 5;

        $tempOtp = $this->userService->generateOtp();
        $createMobileOtp = TempOtp::updateOrCreate(
            ['mobile' => $request->mobile],
            [
                'uuid' => Str::uuid(),
                'email' => null,
                'mobile' => $request->mobile,
                'country_code' => $request->country_code,
                'service' => 'sms',
                'type' => 'sms',
                'otp' => $tempOtp,
                'expire_at' => Carbon::now()->addMinute($expireTime)
            ]
        );

        if ($createMobileOtp) {
            $this->userService->sendOtpService($createMobileOtp);
            return true;
        } else {
            return false;
        }
        // } catch (Exception $e) {
        //     $errorMessage = $e->getMessage();
        //     $errorResponseMessage = $errorMessage != null ? $errorMessage :  __('error_messages.system_error');
        //     throw new Exception($errorResponseMessage, $e->getCode());
        // }
    }

    //send otp via mail on email
    public function sentEmailOtp($email)
    {
        // return true;
        // try {
        $expireTime = config('al_config.otp_expire') ? config('al_config.otp_expire') : 5;
        $tempOtp = $this->userService->generateOtp();
        $createMobileOtp = TempOtp::updateOrCreate(
            ['email' => $email],
            [
                'uuid' => Str::uuid(),
                'email' => $email,
                'mobile' => null,
                'service' => 'email',
                'otp' => $tempOtp,
                'expire_at' => Carbon::now()->addMinute($expireTime)
            ]
        );

        if ($createMobileOtp) {
            $this->userService->sendOtpService($createMobileOtp);
            return true;
        } else {
            return false;
        }
        // } catch (Exception $e) {
        //     $errorMessage = $e->getMessage();
        //     $errorResponseMessage = $errorMessage != null ? $errorMessage :  __('error_messages.system_error');
        //     throw new Exception($errorResponseMessage, $e->getCode());
        // }
    }


    //get user details using username/email
    public function checkOtp($username, $otp)
    {
        $validateOtpResponse = [
            'status' => 'invalid',
            'customUserMessageTitle' => __('error_messages.invalid_otp_title'),
            'customUserMessageText' => __('error_messages.invalid_otp_text')
        ];
        $details = TempOtp::where('otp', $otp)
            ->where(function ($q) use ($username) {
                $q
                    ->Where('email', strtolower($username))
                    ->orWhere('mobile', strtolower($username));
            })
            ->first();
        if (isset($details)) {
            $currentDate = Carbon::now();

            if ($currentDate->lessThan($details->expire_at) == true) {
                $tempOTPCreated = TempOtp::where('uuid', $details->uuid)->update([
                    'expire_at' =>  Carbon::now()
                ]);
                $validateOtpResponse = [
                    'status' => 'validate',
                    'customUserMessageTitle' => __('messages.otp_verify_success_title'),
                    'customUserMessageText' => __('messages.otp_verify_success_text')
                ];
            } else {
                $validateOtpResponse = [
                    'status' => 'invalid',
                    'customUserMessageTitle' => __('error_messages.expired_otp_title'),
                    'customUserMessageText' => __('error_messages.expired_otp_text')
                ];
            }
        }
        return $validateOtpResponse;
    }

    //get user details using username/email
    public function getUserByEmailOrUsername($email)
    {
        return AuthUser::with('settings')
            ->where('email', strtolower($email))
            ->orWhere('username', strtolower($email))
            ->first();
    }


    //get user details using username/email
    public function getUserByMobile($mobile)
    {
        return AuthUser::with('settings')
            ->where('mobile', $mobile)->first();
    }

    //get user details using username/email/mobile
    public function getUserByEMU($username)
    {
        return AuthUser::with('settings')
            ->where('email', strtolower($username))
            ->orWhere('username', strtolower($username))
            ->orWhere('mobile', strtolower($username))
            ->first();
    }

    public function logout()
    {
        // try {
        Log::channel('auth')->info('User Logout');
        $user = Auth::user();
        $request = new Request;
        $request['last_logout_at'] = Carbon::now();
        $this->updateAuthUserSetting($user, $request);
        $this->tokenService->deleteSanctumToken($user);
        return [
            'status' => 'success',
        ];
        // } catch (Exception $e) {
        //     $errorMessage = $e->getMessage();
        //     $errorResponseMessage = $errorMessage != null ? $errorMessage :  __('error_messages.system_error');
        //     throw new Exception($errorResponseMessage, $e->getCode());
        // }
    }
    public function updateAuthUserSetting($model, $request)
    {
        $model_name = $model->getMorphClass();
        $model_id = $model->id;
        $userSettingDetails = AuthSetting::where('model_name', $model_name)->where('model_id', $model_id)->latest()->first();
        if (empty($userSettingDetails)) {
            $authSetting = AuthSetting::create(
                [
                    'uuid' => Str::uuid(),
                    'model_name' => $model->getMorphClass(),
                    'model_id' => $model->id,
                    'user_type' => $model->user_type ? $model->user_type : 'app_user',
                    'user_status' => 1,
                    'registration_at' => Carbon::now(),
                    'email_verified_at' => $model->email_verified_at,
                    'last_login_at' => $model->last_login_at,
                    'last_logout_at' => $model->last_logout_at,
                ]
            );

            return [
                'status' => 'success',
                'data' => $authSetting
            ];
        } else {
            $authSetting = AuthSetting::where('uuid', $userSettingDetails->uuid)->update(
                [
                    'user_type' => !empty($request->user_type) ? $request->user_type : $userSettingDetails->user_type,
                    'user_status' => !empty($request->user_status) ? $request->user_status : $userSettingDetails->user_status,
                    'registration_at' => !empty($request->registration_at) ? $request->registration_at : $userSettingDetails->registration_at,
                    'email_verified_at' => !empty($request->email_verified_at) ? $request->email_verified_at : $userSettingDetails->email_verified_at,
                    'last_login_at' => !empty($request->last_login_at) ? $request->last_login_at : $userSettingDetails->last_login_at,
                    'last_logout_at' => !empty($request->last_logout_at) ? $request->last_logout_at : $userSettingDetails->last_logout_at
                ]
            );
            return [
                'status' => 'success',
                'data' => $authSetting
            ];
        }
    }

    public function webEmailVerification($token)
    {
        $isEmailVerificationComplete = false;
        $decrypt = Crypt::decryptString($token);
        $decrypt = decrypt($decrypt);
        $email_encryption_key = config('al_auth_config.email_encryption_key');
        $explode = explode($email_encryption_key, $decrypt);
        if (!empty($explode[2])) {
            $currentDate = Carbon::now();
            $requestDate = $explode[2];
            if ($currentDate->diffInHours($requestDate) <= config('al_auth_config.email_verification_mail_expiry')) {
                $tempUserDetails = TempRegistration::where('email', $explode[1])->where('uuid', $explode[0])->latest()->first();
                if (!empty($tempUserDetails)) {
                    $tempUserDetails['email_verified_at'] = Carbon::now();
                    // return $tempUserDetails;
                    $user = new AuthUser;
                    $currentUserDetails = AuthUser::where('email', $tempUserDetails)->latest()->first();

                    $userDetails = TempRegistration::where('uuid', $tempUserDetails->uuid)->update([
                        'email_verified_at' => Carbon::now(),
                        'status' => 'verified'
                    ]);
                    if (!empty($currentUserDetails)) {
                        $updateMainTable = AuthUser::where('uuid', $currentUserDetails->uuid)->update([
                            'email_verified_at' => Carbon::now()
                        ]);
                        $model_name = $currentUserDetails->getMorphClass();
                        $updateUserSetting = AuthSetting::where('model_id', $currentUserDetails->id)->where('model_name', $model_name)->update([
                            'email_verified_at' => Carbon::now(),
                            'registration_at' => Carbon::now()
                        ]);
                        $isEmailVerificationComplete = true;
                    } else {
                        // create user row into main table
                        $updateMainTable = $this->CreateMainTableEntry($tempUserDetails, $user);
                        $isEmailVerificationComplete = true;
                    }
                }
            }
        }
        return $isEmailVerificationComplete;
    }

    //check reset password token validation
    public function webResetPasswordTokenValidate($token)
    {
        $data = [
            'isTokenValidate' => false,
            'userDetails' => null
        ];
        if (isset($token)) {
            $decrypt = Crypt::decryptString($token);
            $decrypt = decrypt($decrypt);
            $email_encryption_key = 'fg_' . config('al_auth_config.email_encryption_key');
            $explode = explode($email_encryption_key, $decrypt);
            if (!empty($explode[2]) && !empty($explode[3])) {
                $tokensDetails = PasswordReset::where(['token' => $explode[3]])->first();
                if (isset($tokensDetails)) {
                    $currentDate = Carbon::now();
                    $requestDate = $explode[2];
                    if ($currentDate->diffInHours($requestDate) <= config('al_auth_config.forgot_password_mail_expiry')) {
                        $userDetails = AuthUser::select('email')->where('email', $explode[1])->where('uuid', $explode[0])->latest()->first();
                        if (!empty($userDetails)) {
                            $data['isTokenValidate'] = true;
                            $data['userDetails'] = $userDetails;
                        }
                    }
                }
            }
        }
        return $data;
    }
}
