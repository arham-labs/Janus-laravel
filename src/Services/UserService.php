<?php

namespace Arhamlabs\Authentication\Services;

use Arhamlabs\Authentication\Jobs\SendMailForgotPasswordJob;
use Arhamlabs\Authentication\Jobs\SendMailOtpJob;
use Arhamlabs\Authentication\Jobs\SendMailVerificationJob;
use Arhamlabs\Authentication\Models\PasswordReset;
use Arhamlabs\NotificationHandler\Jobs\SmsNotificationHandlerJob;
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

    public function SendMailOtpService($data)
    {
        $details = [
            'type' => 'email',
            'otp' => $data->otp,
            'email' => $data->email ? $data->email : null,
            'mobile' => $data->mobile ? $data->mobile : null,
            'subject' =>  __('messages.mail_subject_otp_verification'),
            'view' => 'mails.sendOtpMail',
            'logo' => url('assets/logo/logo.png'),
        ];
        dispatch(new SendMailOtpJob($details));
    }

    public function sendSmsOtpService($data)
    {
        $details = [
            'type' => 'sms',
            'otp' => $data->otp,
            'country_code' => $data->country_code ? $data->country_code : null,
            'mobile' => $data->mobile ? $data->mobile : null,
            'to' => $data->country_code . $data->mobile,
            'body' => __('messages.sms_otp_title', ['otp' => $data->otp])
        ];
        dispatch(new SmsNotificationHandlerJob($details));
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
            'subject' =>  __('messages.mail_subject_email_verification'),
            'view' => 'mails.sendVerificationMail',
            'logo' => url('assets/logo/logo.png'),
            'tokenUrl' => url("email/verification/$token")
        ];
        dispatch(new SendMailVerificationJob($data));
    }

    public function SendResetPasswordLinkService($data)
    {
        $date = Carbon::now();
        $email_encryption_key = 'fp_' . config('al_auth_config.email_encryption_key');
        $tokenKey = Hash::make(Str::random(6));
        $en = encrypt($data->uuid .  $email_encryption_key . $data->email .  $email_encryption_key . $date . $email_encryption_key . $tokenKey);
        $token = Crypt::encryptString($en);

        $createEntry = PasswordReset::updateOrCreate(
            ['email' => $data->email],
            [
                'email' => $data->email,
                'token' => $tokenKey
            ]
        );
        if (isset($createEntry)) {
            $data = [
                'type' => 'email',
                'email' => $data->email,
                'mobile' => $data->mobile,
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'name' => $data->first_name . ' ' . $data->last_name,
                'subject' => __('messages.mail_subject_forgot_password'),
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
