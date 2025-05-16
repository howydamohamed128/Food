<?php

namespace App\Actions\User;

use App\Exceptions\APIException;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class DepositBalanceAction {
    use AsAction;

    /**
     * @throws APIException
     */
    public function handle(User $user, float $amount, $message) {

        if ($amount <= 0) {
            return;
        }

        $user->deposit($amount, [
            'description' => $message
        ]);

    }

}
