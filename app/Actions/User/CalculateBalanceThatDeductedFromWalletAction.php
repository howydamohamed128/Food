<?php

namespace App\Actions\User;

use App\Exceptions\APIException;
use App\Models\User;
use Cknow\Money\Money;
use Lorisleiva\Actions\Concerns\AsAction;

class CalculateBalanceThatDeductedFromWalletAction {
    use AsAction;

    /**
     * @throws APIException
     */
    public function handle(User $user, $amount) {
        if ($user->balance > $amount) {
            return $amount;
        }
        return $user->balance;

    }

}