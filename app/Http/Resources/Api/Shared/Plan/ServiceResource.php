<?php

namespace App\Http\Resources\Api\Shared\Plan;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request) {
        return [
            'id' => $this->service->id,
            'name' => $this->service->title,
            'description' => $this->service->description,
            'image' => $this->service->getFirstMediaUrl(),
            'price' => number_format($this->service->price,2),
            // 'duration' => $this->duration,
        ];
    }
}