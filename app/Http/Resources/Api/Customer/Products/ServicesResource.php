<?php

namespace App\Http\Resources\Api\Customer\Products;

use App\Enum\ServiceReservationTypesEnum;
use App\Http\Resources\Api\Shared\CategoryResource;
use App\Http\Resources\Api\Shared\ServiceTypeResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Lib\Utils;
use App\Models\Catalog\Product;

class ServicesResource extends JsonResource
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
            'images' => $this->images_text,
            'implementation_periods' => ((int)number_format($this->implementation_periods / 60, 2)) . ' ' . __('forms.suffixes.hour'),
            'warranty_period' => ((int) $this->warranty_period) . ' ' . __('forms.suffixes.days'),
            'deposit' => $this->deposit . ' ' . '%',
            // 'favorite' => (bool)$request->user('sanctum')?->isFavorited($this),
            $this->mergeWhen($this->service_type == 'plan', [
                "services" => $this->services ? \App\Http\Resources\Api\Shared\Plan\ServiceResource::collection($this->services) : null,
            ]),

        ];
    }
}
