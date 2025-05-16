<?php

namespace App\Http\Requests\Api\Customer;

use App\Rules\IsValidCoupon;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\KSAPhoneRule;

class ApplyCouponRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            "code" => ["required", new IsValidCoupon],

        ];
    }


}
