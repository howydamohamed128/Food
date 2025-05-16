<?php

namespace App\Http\Resources\Api\Customer\Orders;

use App\Enum\ServiceReservationTypesEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class LightOrderProductResource extends JsonResource
{


    public function toArray($request)
    {
        return [
            'id' => $this->product->id,
            'title' => $this->product->title,
            'description' => $this->product->description,
            'image' => $this->product->getFirstMediaUrl(),
            'price' => $this->product->price,
        ];
    }
}
