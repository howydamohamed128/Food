<?php

namespace App\Actions\Authentication;

use Lorisleiva\Actions\Concerns\AsAction;


class SendOTPCodeAction {
    use AsAction;

    public function handle($user) {
        ForgetPassword::run($user);

    }

}
