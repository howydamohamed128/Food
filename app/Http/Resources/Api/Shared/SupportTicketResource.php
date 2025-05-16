<?php

namespace App\Http\Resources\Api\Shared;

use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request) {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'reply' => $this->reply,
            'replied_at' => $this->replied_at?->translatedFormat('Y-m-d h:i a'),
            'created_at' => $this->created_at?->translatedFormat('Y-m-d h:i a'),
        ];
    }
}
