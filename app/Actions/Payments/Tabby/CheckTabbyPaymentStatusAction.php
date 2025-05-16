<?php

namespace App\Actions\Payments\Tabby;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Enum\OrderStatus;
use App\Lib\TabbyService;
use App\Models\Order;

class CheckTabbyPaymentStatusAction {
    use AsAction;

    public function handle(): void {

        $tabby = new TabbyService();
        \Log::info('Tabby Payment callback fired', request()->all());
        $order = Order::where('payment_data->invoiceId', request()->get('id'))->first();

        $response = $tabby->getSession($order->payment_data['session_id']);
        if (isset($response->payment) && $response->payment->status == "CLOSED") {
            $order->update([
                'status' => OrderStatus::PROCESSING,
                'payment_status' => 'paid',


            ]);
        }
    }

}
