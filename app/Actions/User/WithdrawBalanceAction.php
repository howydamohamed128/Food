<?php

namespace App\Actions\User;

use App\Exceptions\APIException;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class WithdrawBalanceAction {
    use AsAction;

    /**
     * @throws APIException
     */
    public function handle(User $user, float $amount, $message) {

        if ($user->balance < $amount) {
            throw  new APIException(__("panel.messages.sufficient_balance"));
        }
        if ($amount <= 0) {
            return;
        }

        $user->withdraw($amount, [
            'description' => $message
        ]);

    }

}
