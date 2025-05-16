<?php

namespace App\Http\Resources\Api\Shared;

use App\Http\Resources\Api\Customer\Products\LightServiceResource;
use App\Http\Resources\Api\Customer\Products\ServiceResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkerResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request) {
        return [
            'id' => $this->id,
            'avatar'=> $this->getFirstMediaUrl(),
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'bio' => $this->data['bio']??'',
            'job_title' => $this->data['job_title']??'',
            'services'=>LightServiceResource::collection($this->services)
        ];
    }
}
