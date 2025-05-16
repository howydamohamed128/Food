<?php

namespace App\Http\Resources\Api\Worker;

use App\Enum\OrderPaymentStatus;
use App\Enum\OrderStatus;
use App\Http\Resources\Api\Customer\AddressBookResource;
use App\Http\Resources\Api\Customer\Branches\BranchResource;
use App\Http\Resources\Api\Customer\Branches\LightBranchResource;
use App\Http\Resources\Api\Customer\Cart\CartProductResource;
use App\Http\Resources\Api\Customer\RateResource;
use App\Http\Resources\Api\Shared\LightCustomerResource;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Str;

class LightOrderResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $cart = $this->as_cart;
        $service = $this->as_cart->getContent()?->first()?->associatedModel;
        return [

            "id" => $this->id,
            "order_number" => $this->order_number,
            'image' => $service?->getFirstMediaUrl(),
            'title' => $service?->title,
            "status" => __("panel.enums." . $this->status->value),
            "status_code" => $this->status,
            'payment_status' => $this->payment_status?->getLabel()??OrderPaymentStatus::PAID->getLabel(),
            'payment_status_enum' => $this->payment_status?->value??OrderPaymentStatus::PAID->value,
            "date" => $this->date?->translatedFormat("Y-m-d") ?? __("Not yet determined"),
            'from' => $this->from,
            'to' => $this->to,
            'slots' => Carbon::parse($this->from)->format('h:i A') . ' - ' . Carbon::parse($this->to)->format('h:i A'),
            "address" => new AddressBookResource($this->address),
            'customer' => new LightCustomerResource($this->customer),
            'created_at' => $this->created_at->diffForHumans(),
            // "totals" => $cart->formattedTotals()['total'],

        ];
    }
}