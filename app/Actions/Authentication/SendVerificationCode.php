<?php

namespace App\Actions\Authentication;

use App\Lib\SMS;
use App\Lib\Utils;
use App\Models\VerificationCode;
use Lorisleiva\Actions\Concerns\AsAction;

class SendVerificationCode {
    use AsAction;

    public function handle($user = null, $phone = null) {
        $verification_method = request()->get('verification_method');
        $code = Utils::randomOtpCode();
        $this->saveOnDb($user, $phone, $code);
        $message = __("panel.messages.otp_message", ['code' => $code],'ar');

        if ($verification_method == 'otp') {
             SMS::init($phone, $message)->send();
        } elseif ($verification_method == 'whatsapp') {
            WhatsApp::sendMessage($phone, $message);
        }

        return $code;
    }

    public function saveOnDb($user, $phone, $code) {
        if (!$user) {
            VerificationCode::create(['phone' => $phone, 'code' => $code]);
        } else {
            $user->verificationCodes()->create([
                'phone' => $phone,
                'code' => $code,
            ]);
        }
    }

}