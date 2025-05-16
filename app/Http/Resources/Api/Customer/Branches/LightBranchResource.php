<?php

namespace App\Http\Resources\Api\Customer\Branches;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Api\V1\Customer\BranchServices;
use App\Lib\Utils;

class LightBranchResource extends JsonResource {


    public function toArray($request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
