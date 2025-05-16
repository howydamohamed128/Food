<?php

namespace App\Http\Resources\Api\Shared;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Lib\Utils;

class FaqResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request) {
        return [
            'id' => $this->id,
            'question' =>Utils::getTranslatedField($this->question),
            'answer'=>Utils::getTranslatedField($this->answer)
        ];
    }
}
