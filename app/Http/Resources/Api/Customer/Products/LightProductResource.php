<?php

namespace App\Http\Resources\Api\Customer\Products;

use App\Enum\ServiceReservationTypesEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class LightProductResource extends JsonResource
{


    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->getFirstMediaUrl(),
            'price' => $this->price,
        ];
    }
}