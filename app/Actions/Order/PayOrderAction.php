<?php

namespace App\Actions\Order;

use App\Actions\User\CalculateBalanceThatDeductedFromWalletAction;
use App\Actions\User\WithdrawBalanceAction;
use App\Models\Order;
use Lorisleiva\Actions\Concerns\AsAction;

class PayOrderAction {
    use AsAction;

    public function handle(Order $order,$payment_method='myfatoorah') {

        $walletBalance = CalculateBalanceThatDeductedFromWalletAction::run($order->customer, $order->as_cart->totals()['wallet']);
        WithdrawBalanceAction::run($order->customer, $walletBalance, [
            'ar' => __("panel.messages.withdraw_from_wallet", ["amount" => $walletBalance, 'order' => $order->id], 'ar'),
            'en' => __("panel.messages.withdraw_from_wallet", ["amount" => $walletBalance, 'order' => $order->id], 'en'),
        ]);
        if ($walletBalance > 0) {
            $order->pay($walletBalance, auth()->user(), 'wallet');
        }
        if ($order->as_cart->getTotal() <= 0) {
            try {
                \Illuminate\Support\Facades\Mail::to($order->customer?->email)->send(new \App\Mail\OrderInvoiceMail($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
            }
        }
        if ($order->as_cart->totals()['deposit'] > 0) {
            $order->pay($order->as_cart->totals()['deposit'], auth()->user(),$payment_method);
        }


    }

}
