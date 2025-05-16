<?php

namespace App\Http\Resources\Api\Shared;

use App\Http\Resources\Api\Customer\Products\LightServiceResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftResource extends JsonResource {

    public function toArray($request) {
        return [
            'id' => $this->id,
            'receiver' => LightCustomerResource::make($this->receiver),
            'service' => [
                'id' => $this->service_id,
                'image' => $this->service->getFirstMediaUrl(),
                'title' => $this->service->title,
                'duration' => __("forms.fields.minute", ["minute" => $this->duration]),
                'quantity' => $this->quantity,
            ],
            'transactions' => TransactionResource::collection($this->transactions),
            'total' => $this->price,
        ];
    }


}
