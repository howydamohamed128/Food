<?php

namespace App\Http\Resources\Api\Shared;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource {

    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // 'description' => $this->description,
            // 'image' => $this->getFirstMediaUrl(),
            // 'has_children' => $this->children()->count() > 0,


        ];
    }
}
