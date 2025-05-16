<?php

namespace App\Http\Resources\Api\Shared\Plan;

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
            'name' => $this->name,
            'price'=> $this->price,
        ];
    }
}
