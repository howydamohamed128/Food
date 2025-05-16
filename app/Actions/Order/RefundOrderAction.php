<?php

namespace App\Actions\Order;

use App\Actions\User\CalculateBalanceThatDeductedFromWalletAction;
use App\Actions\User\WithdrawBalanceAction;
use App\Enum\OrderPaymentStatus;
use App\Models\Order;
use App\Settings\GeneralSettings;
use Lorisleiva\Actions\Concerns\AsAction;

class RefundOrderAction {
    use AsAction;

    public function handle(Order $order) {

        $refundRule = (new GeneralSettings())->refund_rules;
        $percentage = data_get($refundRule, 'refunded_percentage', 100);
        $duration = data_get($refundRule, 'duration', 60);
        $amount = $order->getActualPaidAmount();
        $order->transaction()->update(['status' => OrderPaymentStatus::REFUNDED]);
        if ($order->payment_status == OrderPaymentStatus::PENDING) {
            return;
        }
        // if ($order->created_at->diffInMinutes(now()) < $duration) {
        //     $response = RefundOrderAmountToBankAccountAction::run($order, $percentage, $amount);

        //     $order->transaction()->update(['meta_data' => [...$order->transaction->meta_data, 'refund' => $response]]);
        //     return;
        // }
        RefundOrderAmountToWalletAction::run($order, $amount ,$percentage);
    }

}
