<?php

namespace App\Http\Resources\Api\Customer\Orders;

use App\Enum\DeliveryMethods;
use App\Enum\Order;
use App\Enum\OrderPaymentStatus;
use App\Http\Resources\Api\Customer\AddressBookResource;
use App\Http\Resources\Api\Customer\Branches\BranchResource;
use App\Http\Resources\Api\Customer\Branches\LightBranchResource;
use App\Http\Resources\Api\Customer\Cart\CartProductResource;
use App\Http\Resources\Api\Customer\CustomerResource;
use App\Http\Resources\Api\Customer\Orders\LightOrderProductResource;
use App\Http\Resources\Api\Customer\Products\CartOptionResource;
use App\Http\Resources\Api\Customer\Products\LightProductResource;
use App\Http\Resources\Api\Customer\RateResource;
use App\Http\Resources\Api\Shared\LightCustomerResource;
use App\Http\Resources\Api\Shared\LightWorkerResource;
use App\Http\Resources\Api\Shared\TransactionResource;
use App\Http\Resources\Api\Shared\WorkerResource;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Str;

class OrdersResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $cart = $this->as_cart;
        $totals = $cart->Totals();
        return [
            "id" => $this->id,
            "order_number" => $this->order_number,
            "created_date" => $this->created_at->translatedFormat("Y-m-d h:i a"),
            "date" => $this->date?->translatedFormat("Y-m-d"),
            'from' => $this->from,
            'to' => $this->to,
            'slots' => Carbon::parse($this->from)->format('h:i A') . ' - ' . Carbon::parse($this->to)->format('h:i A'),
            "status" => __("panel.enums." . $this->status->value),
            "status_code" => $this->status,
            'payment_status' => $this->payment_status?->getLabel()??OrderPaymentStatus::PAID->getLabel(),
            'payment_status_enum' => $this->payment_status?->value??OrderPaymentStatus::PAID->value,
            'service' => CartProductResource::make($this->as_cart->getContent()->first()),
            'addons' => LightOrderProductResource::collection($this->adds),
            'options' => OrderOptionResource::collection($this->options),
            'worker' => LightWorkerResource::make($this->worker),
            'customer' => LightCustomerResource::make($this->customer),
            'address' => AddressBookResource::make($this->address),
            $this->mergeWhen($this?->rated(), [
                'rating' => RateResource::make($this?->rate)
            ]),
            "can_rate" => $this->canRate(),
            'notes' => $this->notes,
            'transactions' => TransactionResource::collection($this->transactions),
            // 'has' => [
            //     'coupon' => $totals['discount'] < 0,
            //     'wallet' => $totals['wallet'] > 0,
            // ],
            "totals" => $cart->Totals(),

        ];
    }
}