<?php

namespace App\Actions\Order;

use App\Actions\User\DepositBalanceAction;
use App\Models\Order;
use Lorisleiva\Actions\Concerns\AsAction;

class RefundOrderAmountToWalletAction {
    use AsAction;

    public function handle(Order $order,$amount,$percentage): void {
        $refundAmount = ($amount -($amount * $percentage) / 100);
        DepositBalanceAction::run($order->customer,
            $refundAmount, [
                'ar' => __("panel.messages.refunded_order", ["id" => $order->id, 'amount' => $refundAmount], 'ar'),
                'en' => __("panel.messages.refunded_order", ["id" => $order->id, 'amount' => $refundAmount], 'en'),
            ]
        );

    }

}
