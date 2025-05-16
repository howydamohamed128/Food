<?php

namespace App\Actions\Payments\Tamara;

use Http;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\Order;

class AuthoriseTamaraPaymentAction {
    use AsAction;

    public Order $order;

    public function token(): string {
        return config('tamara.token');
    }

    public function handle(Order $order) {
        $id = $order->payment_data['session_id'] ?? 0;
        $this->order = $order;
        $response = Http::withToken($this->token())->post("https://api-sandbox.tamara.co/orders/$id/authorise");

        \Log::info("Order $order->id authorized", $response->json());


    }
}
