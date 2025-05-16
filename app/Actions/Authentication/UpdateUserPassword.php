<?php

namespace App\Actions\Authentication;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUserPassword {
    use AsAction;

    public function handle(User $user, $password) {
        $user->update(['password' => $password]);
        UpdateUserToken::run($user);
        RemoveVerficationCodes::run($user);
    }

}
