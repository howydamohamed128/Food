<?php

namespace App\Http\Resources\Api\Customer\Cart;

use App\Http\Resources\Api\Customer\Products\CartOptionResource;
use App\Http\Resources\Api\Customer\Products\LightProductResource;
use App\Http\Resources\Api\Shared\LightWorkerResource;
use App\Models\Catalog\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class CartResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $totals = $this->totals();
        return [
            "service" => CartProductResource::make($this->getContent()->values()[0]),

            'adds' => LightProductResource::collection($this->getAdd()??[]),
            'options' => CartOptionResource::make($this->getOptions()??[]),
            'date' => $this->getDate(),
            'slot' => $this->getSlot(),
            'notes' => $request->get('notes'),
            // 'has' => [
            //     'coupon' => $totals['discount'] > 0,
            //     'wallet' => $totals['wallet'] > 0,
            // ],

            'totals' => $this->Totals(),


        ];
    }

}
