<?php

namespace App\Actions\Payments\Tabby;

use Cknow\Money\Money;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Exceptions\APIException;
use App\Lib\TabbyService;
use App\Models\Order;

class CreateTabbyInvoiceAction {
    use AsAction;

    public function handle(Order $order) {
        $totals = $order->as_cart->totals();
        $items = collect([]);
        foreach ($order->itemsLine as $item) {
            $price = Money::parse((1 * $item->price) + (1 * collect($item->conditions)->sum('value')))->formatByDecimal();
            $items->push([
                'title' => $item->name,
                'quantity' => 1,
                'unit_price' => $price,
                'category' => 'Meats',
            ]);
        }

        $order_data = [
            'amount' => $order->total->formatByDecimal(),
            'currency' => 'SAR',
            'description' => 'description',
            'full_name' => $order->customer?->name,
            'buyer_phone' => $order?->customer?->phone,
            'buyer_email' => $order->customer?->email??'',
            'address' => isset($order->address?->name)?$order->address?->name:"Riyadh",
            'city' => isset($order->address?->city?->name)?$order->address?->city?->name:'Riyadh',
            'zip' => '1234',
            'order_id' => "$order->id",
            'registered_since' => now()->toIso8601String(),
            'loyalty_level' => 0,
            'success-url' => route('webhooks.tabby.success'),
            'cancel-url' => route('webhooks.tabby.cancel'),
            'failure-url' => route('webhooks.tabby.failure'),
            'items' => $items->toArray(),
            "taxes_amount" => (string)$totals['taxes'],
            "shipping_amount" => (string)$totals['delivery'],
            "discount_amount" => $totals['discount'] ?? "0",

        ];

        $tabby = new TabbyService();

        $payment = $tabby->createSession($order_data);

        $id = $payment->id;
        if (!isset($payment->configuration->available_products->installments[0]->web_url)) {
            match ($payment->rejection_reason_code) {
                'not_enough_limit' => throw new APIException(__("validation.api.".$payment->configuration?->products?->installments?->rejection_reason)),
                default => throw new APIException($payment->rejection_reason_code),
            };

        }
        $redirect_url = $payment->configuration->available_products->installments[0]->web_url;
        $order->update(['payment_data' => [
            'session_id' => $id,
            'invoiceId' => $payment->payment->id,
            'invoiceURL' => $redirect_url,
            'gateway' => 'tabby',
            'method' => 'tabby'
        ]]);
        return [
            'session_id' => $id,
            'invoiceId' => $payment->payment->id,
            'invoiceURL' => $redirect_url,
            'gateway' => 'tabby',
            'method' => 'tabby'
        ];


    }

}
