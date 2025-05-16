<?php

namespace App\Http\Resources\Api\Customer\Branches;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Api\V1\Customer\BranchServices;
use App\Lib\Utils;

class BranchResource extends JsonResource {


    public function toArray($request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address_name,
            'location' => [
                'lat' => $this->location->getCoordinates()[1],
                'lng' =>  $this->location->getCoordinates()[0],
            ],

        ];
    }
}
