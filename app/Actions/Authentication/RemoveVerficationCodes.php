<?php

namespace App\Actions\Authentication;

use App\Models\User;
use App\Models\VerificationCode;
use Lorisleiva\Actions\Concerns\AsAction;

class RemoveVerficationCodes {
    use AsAction;

    public function handle(User $user) {
        return VerificationCode::where(['phone' => $user->phone])->delete();
    }

}
