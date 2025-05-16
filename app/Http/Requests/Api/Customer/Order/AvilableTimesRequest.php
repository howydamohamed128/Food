<?php

namespace App\Http\Requests\Api\Customer\Order;

use App\Enum\Catalog\OptionTypes;
use App\Enum\ServiceReservationTypesEnum;
use App\Models\Catalog\Product;
use App\Models\Catalog\Service;
use App\Models\Option;
use App\Rules\IsValidCoupon;
use App\Rules\IsValidCouponOrder;
use App\Rules\WorkerServeServiceRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class AvilableTimesRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {

        $rules = [
            'address_id' => 'required|exists:addresses,id',
            'service_id' => 'required|exists:products,id',
            'date' => ['required', 'date', 'after_or_equal:' . now()->format('Y-m-d')],

        ];

        return $rules;
    }


}