<?php

namespace App\Http\Resources\Api\Customer\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ServicePricingResource extends JsonResource {


    public function toArray($request) {
        return [
            'id'=> $this['id'],
            'price' => $this['price'],
            'slot' => __("forms.fields.minute", ['minute' => $this['id']]),
        ];
    }
}
