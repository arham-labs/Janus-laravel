<?php

namespace Arhamlabs\Authentication\Services;

use Arhamlabs\Authentication\Jobs\SendMailForgotPasswordJob;
use Arhamlabs\Authentication\Jobs\SendMailOtpJob;
use Arhamlabs\Authentication\Jobs\SendMailVerificationJob;
use Arhamlabs\Authentication\Jobs\SendOtpJob;
use Arhamlabs\Authentication\Models\PasswordReset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class UserService
{


    public function generateOtp()
    {
        $digits = config('al_auth_config.otp_length') ? config('al_auth_config.otp_length') : 4;
        return  $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    }

    public function tokenExpiry()
    {
        return date('Y-m-d h:i:s', strtotime('+3 month'));
    }

    public function SendOtpService($data)
    {
        $details = [
            'type' => $data->type,
            'otp' => $data->otp,
            'email' => $data->email ? $data->email : null,
            'mobile' => $data->mobile ? $data->mobile : null,
            'subject' => 'OTP VERIFICATION',
            'view' => 'mails.sendOtpMail',
            'logo' => url('assets/logo/logo.png'),
        ];
        if ($data->type == 'sms')
            dispatch(new SendOtpJob($details));
        else
            dispatch(new SendMailOtpJob($details));
    }
    public function SendMailVerificationService($data)
    {
        $date = Carbon::now();
        $email_encryption_key = config('al_auth_config.email_encryption_key');
        $en = encrypt($data->uuid .  $email_encryption_key . $data->email .  $email_encryption_key . $date);
        $token = Crypt::encryptString($en);

        $data = [
            'type' => 'email',
            'email' => $data->email,
            'mobile' => $data->mobile,
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'name' => $data->first_name . ' ' . $data->last_name,
            'subject' => 'Email Verification',
            'view' => 'mails.sendVerificationMail',
            'logo' => url('assets/logo/logo.png'),
            'tokenUrl' => url("email/verification/$token")
        ];
        dispatch(new SendMailVerificationJob($data));
    }

    public function SendResetPasswordLinkService($data)
    {
        $date = Carbon::now();
        $email_encryption_key = 'fg_' . config('al_auth_config.email_encryption_key');
        $tokenKey = Hash::make(Str::random(6));
        $en = encrypt($data->uuid .  $email_encryption_key . $data->email .  $email_encryption_key . $date . $email_encryption_key . $tokenKey);
        $token = Crypt::encryptString($en);
        //   return  $token = Hash::make(encrypt(Str::random(6)));

        $createEntry = PasswordReset::create([
            'email' => $data->email,
            'token' => $tokenKey
        ]);
        if (isset($createEntry)) {
            $data = [
                'type' => 'email',
                'email' => $data->email,
                'mobile' => $data->mobile,
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'name' => $data->first_name . ' ' . $data->last_name,
                'subject' => 'Forgot Password',
                'view' => 'mails.sendForgotPasswordMail',
                'logo' => url('assets/logo/logo.png'),
                'tokenUrl' => url("reset-password/$token")
            ];
            dispatch(new SendMailForgotPasswordJob($data));
        } else {
            Log::debug("Password reset table entry issue");
        }
    }
}
