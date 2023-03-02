<?php

namespace Arhamlabs\Authentication\Services;

use Arhamlabs\Authentication\Jobs\SendMailVerificationJob;
use Arhamlabs\Authentication\Jobs\SendOtpJob;
use Arhamlabs\Authentication\Mail\SendOtpMail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


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
            dispatch(new SendOtpMail($details));
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
}
