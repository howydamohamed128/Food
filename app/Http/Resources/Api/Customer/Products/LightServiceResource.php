<?php

namespace App\Http\Resources\Api\Customer\Products;

use App\Enum\ServiceReservationTypesEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class LightServiceResource extends JsonResource
{


    public function toArray($request)
    {
        $avg = round($this->rates()->avg('rate') ?? 0, 2);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->getFirstMediaUrl(),
            'price' =>  number_format($this->price, 2),
            'rate' => ($avg == (int) $avg) ? (int) $avg : $avg,
            'rates_count' => $this->rates()->count(),
            // 'favorite' => (bool)$request->user('sanctum')?->isFavorited($this),
        ];
    }
}
