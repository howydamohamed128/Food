<?php

namespace App\Http\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Shared\ZoneResource;

class AddressBookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            'title' => $this?->title ?? '',
            'location' => $this->location,
            'national_code' => $this->national_code ?? '',
            'primary' => $this->primary == 1 ? true : false,
        ];
    }
}