<?php

namespace App\Lib;

use Http;
use App\Enum\DeliveryMethods;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class SMS {
    public static function make() {
        return new static();
    }


    public function send($phone, $message) {
        $response = \Illuminate\Support\Facades\Http::withQueryParameters([
            'bearerTokens' => env('SMS_TOKEN', '5012db787880b5db6156582e594cad95'),
            'sender' => 'Selfcspa',
            'recipients' => $phone,
            'body' => $message
        ])->post('https://api.taqnyat.sa/v1/messages')
            ->json();

        Log::info("send sms to {$phone}", $response);
        return $response;

    }
}
