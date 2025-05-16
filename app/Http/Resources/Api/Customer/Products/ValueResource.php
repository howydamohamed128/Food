<?php

namespace App\Http\Resources\Api\Customer\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ValueResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this?->id,
            'option_id' => $this?->option_id,
            'value' => $this?->value,
            'image' => $this?->getFirstMediaUrl('option_values') ?? '',
            'sort' => $this?->sort,
        ];
    }
}
