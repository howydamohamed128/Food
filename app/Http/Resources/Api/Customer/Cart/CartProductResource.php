<?php

namespace App\Http\Resources\Api\Customer\Cart;

use Arr;
use Cknow\Money\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class CartProductResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {

        $title = $this->associatedModel->title;
        if (is_array($title)) {
            $title = $title[app()->getLocale()] ?? $title[app()->getLocale() == 'ar' ? 'en' : 'ar'];
        }
        return [
            'id' => $this->associatedModel->id,
            'image' => $this->associatedModel->getFirstMediaUrl(),
            "name" => $title ?? '',
            'price' => Money::parse($this->getPriceSumWithConditions())->formatByDecimal(),
            'deposit' => ($this->associatedModel->deposit). ' %',
            'expiration_date' => now()->addDays($this->associatedModel->warranty_period)->format('Y-m-d'),
        ];
    }

}