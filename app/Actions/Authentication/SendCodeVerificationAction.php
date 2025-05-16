<?php

namespace App\Actions\Authentication;


use App\Actions\Shared\Authentication\Utilities;
use Lorisleiva\Actions\Action;

class SendCodeVerificationAction extends Action {

    public function __construct($user,$phone=null) {
        $user->verificationCodes()->delete();
        $code = Utilities::generateActivationCode();
        $user->verificationCodes()->create(['phone' => $phone ?? $user->phone, "code" => $code]);

    }
}
