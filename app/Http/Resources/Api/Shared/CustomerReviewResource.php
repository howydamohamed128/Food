<?php

namespace App\Http\Resources\Api\Shared;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerReviewResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request) {
        return [
            'id' => $this->id,
            'customer_name' => $this->customer_name,
            'rate'=>$this->rate,
            'review'=>$this->review
        ];
    }
}
