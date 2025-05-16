<?php

namespace App\Http\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Brands\Resources\Api\BrandResources;
use App\Fleet\Http\Resources\DriverResource;
use App\Jobs\Http\Resources\Api\RateResources;
use App\Orders\Resources\Api\AddressOrderResource;
use App\Orders\Resources\Api\UserOrderResource;

class RateResource extends JsonResource {

    public function toArray($request) {
        return [
            "rate" => $this->rate,
            "comment" => $this->comment,
        ];
    }
}
