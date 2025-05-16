<?php

namespace App\Actions\Authentication;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;
use Notification;


class VerifyUserAccount {
    use AsAction;

    public function handle(User $user) {
        $user->update(['phone_verified_at' => now(),'deleted_at'=>null]);
        UpdateUserToken::run($user);
        RemoveVerficationCodes::run($user);



    }

}
