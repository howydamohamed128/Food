<?php

namespace App\Actions\Authentication;


use Lorisleiva\Actions\Concerns\AsAction;

class ForgetPassword {
    use AsAction;

    public function handle($user) {
//        $code = Utils::randomOtpCode();
//        $user->verificationCodes()->create(['phone' => $phone ?? $user->phone, "code" => $code]);
        SendVerificationCode::run($user);


    }

}
