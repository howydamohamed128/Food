<?php

namespace App\Http\Resources\Api\Shared;

use Illuminate\Http\Resources\Json\JsonResource;

class LightWorkerResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request)
    {
        $ratings = $this->orders()
            ->whereHas('rate')
            ->with('rate')
            ->get()
            ->pluck('rate')
            ->flatten();

        $avg = round($ratings->avg('rate') ?? 0, 2);
        $count = $ratings->count() ?? 0;

        return [
            'id' => $this->id,
            'avatar' => $this->getFirstMediaUrl(),
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'rate' => ($avg == (int) $avg) ? (int) $avg : $avg,
            'rate_count' => $count ?? 0,
        ];
    }
}