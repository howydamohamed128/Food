<?php

namespace App\Actions\Payments\Tamara;

use Cknow\Money\Money;
use Http;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Exceptions\APIException;
use App\Lib\TabbyService;
use App\Models\Order;

class CreateTamaraInvoiceAction {
    use AsAction;

    public Order $order;

    public function token(): string {
        return config('tamara.token');
    }

    /**
     * @throws APIException
     */
    public function handle(Order $order) {
        $url = config('tamara.mode') == 'sandbox' ? "https://api-sandbox.tamara.co/checkout" : "https://api.tamara.co/checkout";
        $this->order = $order;
        $response = Http::withToken($this->token())->post($url, $this->getData($order));
//        \Log::info('Tamara', $response->json());
        if ($response->status() == 400) {
            throw new APIException($response->json()['message'] ?? '');
        }
        $order->update([
            'payment_data' => [
                'session_id' => $response['order_id'],
                'invoiceId' => $response['checkout_id'],
                'invoiceURL' => $response['checkout_url'],
                'gateway' => 'tamara',

                'method' => 'tamara'
            ]
        ]);

        return [
            'session_id' => $response['order_id'],
            'invoiceId' => $response['checkout_id'],
            'invoiceURL' => $response['checkout_url'],
            'gateway' => 'tamara'
        ];

    }

    public function getData($order) {
        $totals = $order->as_cart->totals();

        return [
            ...$this->getTotals(),
            'consumer' => $this->getCustomer(),
            "items" => $this->getItems(),
            "country_code" => "SA",
            "description" => "Enter order description here.",
            "merchant_url" => [
                "cancel" => route('webhooks.tamara.cancel'),
                "failure" => route('webhooks.tamara.failure'),
                "success" => route('webhooks.tamara.success'),
                "notification" => route('webhooks.tamara.callback')
            ],
            "payment_type" => "PAY_BY_INSTALMENTS",
            "instalments" => 3,
            "billing_address" => $this->getShippingAddress(),
            "shipping_address" => $this->getShippingAddress(),
            "platform" => "Naeem",
            "is_mobile" => false,
            "locale" => "ar_SA",
        ];
    }

    public function getShippingAddress(): array {
        return [
            "city" => $this->order?->address?->city?->name??'Riyadh',
            "country_code" => "SA",
            "first_name" => $this->order?->customer?->name,
            "last_name" => "",
            "line1" => $this->order->address?->district?->name??"Riyadh",
            "line2" => "",
            "phone_number" => $this->order?->customer?->phone,
            "region" => " "
        ];
    }

    public function getCustomer(): array {
        return [
            "email" => $this->order?->customer->email ?? fake()->email,
            "first_name" => $this->order?->customer->name,
            "last_name" => "",
            "phone_number" => $this->order?->customer->phone
        ];
    }

    public function getTotals(): array {
        $totals = $this->order->as_cart->totals();

        return [
            "total_amount" => [
                "amount" => $this->order->total->formatByDecimal(),
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
            "order_reference_id" => "{$this->order->id}",
            "order_number" => "{$this->order->id}",
            "discount" => [
                "name" => "Discount",
                "amount" => [
                    "amount" => $totals['discount'] ?? 0,
                    "currency" => "SAR"
                ]
            ],
        ];
    }

    public function getItems(): Collection {
        $items = collect([]);
        foreach ($this->order->itemsLine as $item) {
            $price = Money::parse((1 * $item->price) + (1 * collect($item->conditions)->sum('value')))->formatByDecimal();
            $total = Money::parse(($item->quantity * $item->price) + ($item->quantity * collect($item->conditions)->sum('value')))->formatByDecimal();
            $items->push([
                "name" => $item->name,
                "type" => "Digital",
                "reference_id" => $item->id,
                "sku" => "SA-{$item->id}",
                "quantity" => $item->quantity,
                "total_amount" => ["amount" => $price, "currency" => "SAR"],
                "unit_amount" => ["amount" => $total, "currency" => "SAR"],

            ]);
        }
        return $items;
    }

}
