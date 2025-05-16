<?php

namespace App\Actions\Payments;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCashInvoiceAction {
    use AsAction;

    public function handle($transactionable, User $user, $total) {
        return $transactionable->transaction()->create([
            'user_id' => $user->id,
            'price' => $total,
            'status' => 'manual',
            'meta_data' => ['gateway' => 'cash']
        ]);
    }

}
