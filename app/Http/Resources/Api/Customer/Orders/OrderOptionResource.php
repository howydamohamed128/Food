<?php

namespace App\Http\Resources\Api\Customer\Orders;

use App\Enum\ServiceReservationTypesEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderOptionResource extends JsonResource
{
    public function toArray($request)
    {
        $mapper = match ($this->option->type->value) {
            'textarea' => 'textarea',
            'radio' => 'radio',
            'checkbox' => 'checkbox',
            'date' => 'date',
        };
        return [
            'id' => $this->option_id,
            'name'  => $this->option->name,
            'type_enum' => $mapper,
            'type' => $this->option->type->getLabel(),
            'values' => ValueResource::collection($this?->values)

        ];
    }
}
