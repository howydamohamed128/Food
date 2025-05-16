<?php

namespace App\Http\Requests\Api\Customer\Order;

use Illuminate\Foundation\Http\FormRequest;
use App\Lib\Utils;
use App\Rules\AddressBelongToAuthUserRule;
use App\Rules\IsProductAvailableInBranchRule;
use App\Rules\IsRequiredProductOptionsRepresentRule;
use App\Rules\IsValidProductOptionsRule;
use App\Rules\IsValidProductOptionValuesRule;


class OrderRateRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        return [
            "rate" => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:512']
        ];
    }

}
