<?php

namespace App\Actions\Payments\Tamara;

use Http;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Actions\Payments\Tabby\CreateTabbyInvoiceAction;
use App\Enum\OrderStatus;
use App\Lib\TabbyService;
use App\Models\Order;

class CheckTamaraPaymentStatusAction {
    use AsAction;

    public function handle(): void {
        \Log::info('Tamara Payment callback', request()->all());
        $order_id = request()->get('order_id');
        $orderModal = Order::where('payment_data->session_id', $order_id)->firstOrFail();

        $response = Http::withToken(config('tamara.token'))->get("https://api-sandbox.tamara.co/orders/$order_id")->collect();

        if ($response['status'] == "approved") {
            AuthoriseTamaraPaymentAction::run($orderModal);
        }
        if ($response['status'] == "authorised") {
            $orderModal->update([
                'status' => OrderStatus::PROCESSING,
                'payment_status' => 'paid',


            ]);
        }
    }

}
