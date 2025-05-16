<?php

namespace App\Actions\Authentication;

use Lorisleiva\Actions\Concerns\AsAction;

class ChangeUserPhone {
    use AsAction;

    public function handle($user,$phone) {
        SendVerificationCode::run($user, $phone);
    }

}
