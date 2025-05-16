<?php

namespace App\Actions\Payments\Tamara;

use Http;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Lib\TabbyService;
use App\Models\Order;

class CaptureTamaraPaymentAction {
    use AsAction;

    public Order $order;

    public function token(): string {
        return config('tamara.token');
    }

    public function handle(Order $order) {
        $url = "https://api-sandbox.tamara.co/payments/capture";
        $this->order = $order;
        $response = Http::withToken($this->token())->post($url, $this->getData($order))->collect();
        \Log::info("Order $order->id payload", $this->getData($order));
        \Log::info("Order $order->id captured", $response->toArray());

    }

    public function getData($order) {

        return [
            'order_id' => $order->payment_data['session_id'] ?? '',
            ...$this->getTotals(),
            "items" => $this->getItems()->toArray(),
            "shipping_info" => $this->getShippingAddress(),
        ];
    }

    public function getShippingAddress(): array {
        return [
            "shipped_at" => now(),
            "shipping_company" => "Naeem",
            "tracking_number" => "{$this->order->id}",
            "tracking_url" => "https://google.com",

        ];
    }


    public function getTotals(): array {
        $totals = $this->order->as_cart->totals();

        return [
            "total_amount" => [
                "amount" => $totals['total'],
                "currency" => "SAR"
            ],
            "shipping_amount" => [
                "amount" => $totals['delivery'],
                "currency" => "SAR"
            ],
            "tax_amount" => [
                "amount" => $totals['taxes'],
                "currency" => "SAR"
            ],
            "discount_amount" => [
                "amount" => $totals['discount'] ?? 0,
                "currency" => "SAR"
            ],
        ];
    }

    public function getItems(): Collection {
        $items = collect([]);
        foreach ($this->order->itemsLine as $item) {
            $price = (1 * $item->price) + (1 * collect($item->conditions)->sum('value'));
            $total = ($item->quantity * $item->price) + ($item->quantity * collect($item->conditions)->sum('value'));
            $items->push([
                "name" => $item->name,
                "type" => "Digital",
                "reference_id" => "$item->id",
                "sku" => "SA-{$item->id}",
                "quantity" => intval($item->quantity),
                "total_amount" => ["amount" => $price, "currency" => "SAR"],

            ]);
        }
        return $items;
    }

}
