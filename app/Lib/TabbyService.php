<?php

namespace App\Lib;

use Illuminate\Support\Facades\Http;

class TabbyService {
    public $base_url = "https://api.tabby.ai/api/v2/";
    public $pk_test = "pk_e23d501c-bc2f-4dc5-9828-f926f4faa01e";
    public $sk_test = "sk_62f8889d-acdc-40ad-a621-aac8c6bffdc2";
//    public $pk_test = "pk_test_a79bae6a-cb16-426d-ae84-8b4ac61f7629";
//    public $sk_test = "pk_test_a79bae6a-cb16-426d-ae84-8b4ac61f7629";

    public function createSession($data) {
        $body = $this->getConfig($data);
        $http = Http::withToken($this->pk_test)
            ->baseUrl($this->base_url);
        $response = $http->post('checkout', $body);

        return $response->object();
    }

    public function getSession($payment_id) {
        $http = Http::withToken($this->sk_test)->baseUrl($this->base_url);

        $url = 'checkout/' . $payment_id;

        $response = $http->get($url);

        return $response->object();
    }

    public function getConfig($data) {

        return [
            "payment" => [
                "amount" => $data['amount'],
                "currency" => $data['currency'],
                "description" => $data['description'],
                "buyer" => [
                    "phone" => $data['buyer_phone'],
                    "email" => $data['buyer_email'],
                    "name" => $data['full_name']
                ],
                "shipping_address" => [
                    "city" => $data['city'],
                    "address" => $data['address'],
                    "zip" => $data['zip'],
                ],
                "order" => [

                    "tax_amount" => $data['taxes_amount'],
                    "shipping_amount" => $data['shipping_amount'],
                    "discount_amount" => $data['discount_amount'],
                    "updated_at" => now()->toIso8601String(),
                    "reference_id" => $data['order_id'],
                    "items" => $data['items']
                ],
                "buyer_history" => [
                    "registered_since" => $data['registered_since'],
                    "loyalty_level" => $data['loyalty_level'],
                ],
            ],
            "lang" => 'ar',
            "merchant_code" => "Naeem",
            "merchant_urls" => [
                "success" => $data['success-url'],
                "cancel" => $data['cancel-url'],
                "failure" => $data['failure-url'],
            ],
            'token' => null
        ];
    }

    public function registerWebhooks() {

        $response = Http::withToken($this->sk_test)
            ->withHeaders(['X-Merchant-Code' => 'Naeem'])
            ->post('https://api.tabby.ai/api/v1/webhooks', [
                'id' => 'webhook_id_live',
                'url' => route('webhooks.tabby.callback'),
                'is_test' => false,
            ]);
        return $response->json();

    }

    public function updateWebhook($id) {

        $response = Http::withToken($this->sk_test)
            ->withHeaders(['X-Merchant-Code' => 'Naeem'])
            ->put("https://api.tabby.ai/api/v1/webhooks/$id", [
                'url' => route('webhooks.tabby.callback'),
                'is_test' => false,
            ]);
        return $response->json();

    }

    public function getWebhooks() {

        $response = Http::withToken($this->sk_test)->withHeaders(['X-Merchant-Code' => 'Naeem'])->get("https://api.tabby.ai/api/v1/webhooks");
        return $response->json();

    }
}
