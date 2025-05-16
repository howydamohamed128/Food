<?php

namespace App\Http\Resources\Api\Customer\User;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResources extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {

        return [
            'id' => $this->id,
            "order_number" => $this->order_number,

            'due_date' => $this->created_at->format("Y-m-d H:i a"),
            'invoice_url' => route('orders.invoice', $this->id),
            'paid_at' => isset($this->payment_data['paid_at']) ? Carbon::parse($this->payment_data['paid_at'])->timezone('africa/cairo')->format('Y-m-d h:i a') : null,
            'method' => $this->payment_data['method'] ?? null,
            'total' => $this->as_cart->getTotal(),
        ];
    }


}
