<?php

namespace App\Http\Resources\Api\Customer\Products;

use App\Enum\ServiceReservationTypesEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class CartOptionResource extends JsonResource
{


    public function toArray($request)
    {
        return $this->resource->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'value' => in_array($item['type'], ['textarea', 'date']) ?  $this->formatValue($item['value']) : null,
                'values' => in_array($item['type'], ['radio', 'checkbox']) ? $this->formatArrayValue($item['value']) : null,

            ];
        });
    }

    private function formatValue($value)
    {
        if (is_array($value)) {
            return array_values($value);
        }

        return $value;
    }
    private function formatArrayValue($value)
    {
        $values = [];

        // if (is_array($value)) {
            foreach ($value as $key => $val) {
                $values[] = [
                    'id' => $key,
                    'value' => $val
                ];
            }
        // }

        return collect($values);
    }
}