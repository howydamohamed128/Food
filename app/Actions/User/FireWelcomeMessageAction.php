<?php

namespace App\Actions\User;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;
use Notification;


class FireWelcomeMessageAction {
    use AsAction;

    public function handle(User $user) {
        $user->update(['remember_token' => now()]);
//        Notification::send(auth()->user(), new CustomerWelcomeMessageNotification());
    }

}
