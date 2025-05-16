<?php

namespace App\Actions\Order;

use App\Actions\User\CalculateBalanceThatDeductedFromWalletAction;
use App\Actions\User\WithdrawBalanceAction;
use App\Models\Order;
use Lorisleiva\Actions\Concerns\AsAction;

class PayAdminOrderAction {
    use AsAction;

    public function handle(Order $order,$payment_method='cash') {

        if ($order->as_cart->getTotal() >= 0) {
            try {
                \Illuminate\Support\Facades\Mail::to($order->customer?->email)->send(new \App\Mail\OrderInvoiceMail($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
            }
        }
        if ($order->as_cart->totals()['total'] > 0) {
            $order->pay($order->as_cart->totals()['total'], auth()->user(),$payment_method);
        }


    }

}
