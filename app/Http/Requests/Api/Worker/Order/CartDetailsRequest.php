<?php

namespace App\Http\Requests\Api\Customer\Order;

use App\Enum\ServiceReservationTypesEnum;
use App\Models\Catalog\Product;
use App\Models\Catalog\Service;
use App\Rules\IsValidCoupon;
use App\Rules\IsValidCouponOrder;
use App\Rules\WorkerServeServiceRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class CartDetailsRequest extends FormRequest {
    protected $stopOnFirstFailure = true;

    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        $isServiceTimed = $this->isServiceTimed();
        return [
            'worker_id' => [Rule::requiredIf($isServiceTimed), 'exists:users,id', new WorkerServeServiceRule()],
            'service_id' => ['required', 'exists:products,id'],
            'date' => ['required', 'date', 'after_or_equal:' . \Carbon\Carbon::now()->format('Y-m-d h:i:a')],
            'interval' => [Rule::requiredIf($isServiceTimed), 'numeric', Rule::in([30, 60, 90, 120])],
            'slot' => [Rule::requiredIf($isServiceTimed), 'date_format:H:i'],
            'notes' => ['nullable', 'string'],
            'coupon_code' => ['nullable', 'string', new IsValidCouponOrder()],
        ];
    }

    public function isServiceTimed(): bool {
        return Product::find($this->get('service_id'))->type == ServiceReservationTypesEnum::TIMED;
    }

}
