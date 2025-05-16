<?php

namespace App\Http\Resources\Api\Customer\Orders;

use App\Models\Value;
use Illuminate\Http\Resources\Json\JsonResource;

class ValueResource extends JsonResource
{

    public function toArray($request)
    {
        // $image = Value::where('id', $this->id)->first()->getFirstMediaUrl('option_values') ?? '';

        $value = '';
        if ($this->option->option->type->value == 'date' || $this->option->option->type->value == 'textarea') {
            $value = $this->value;
        }
        if ($this->option->option->type->value == 'radio' || $this->option->option->type->value == 'checkbox') {
            $value = Value::where('id', $this->value)->first()->value;
        }
        return [
            'id' => $this->id,
            'value' => $value,
            // 'image' => $this?->getFirstMediaUrl('option_values') ?? '',
        ];
    }
}
