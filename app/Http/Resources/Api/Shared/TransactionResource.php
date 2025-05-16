<?php

namespace App\Http\Resources\Api\Shared;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource {

    public function toArray($request) {
        return [
            'payment_status' => $this->status?->getLabel(),
            'gateway' => $this->meta_data['gateway'],
            'url' => $this->meta_data['invoiceURL'] ?? null,
            'id' => $this->meta_data['invoiceId'] ?? null,


        ];
    }
}
