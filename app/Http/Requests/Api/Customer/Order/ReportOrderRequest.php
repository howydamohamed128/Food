<?php

namespace App\Http\Requests\Api\Customer\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Lib\Utils;
use App\Rules\AddressBelongToAuthUserRule;
use App\Rules\IsProductAvailableInBranchRule;
use App\Rules\IsRequiredProductOptionsRepresentRule;
use App\Rules\IsValidProductOptionsRule;
use App\Rules\IsValidProductOptionValuesRule;


class ReportOrderRequest extends FormRequest {

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
            "reason_id" => ['nullable', Rule::exists('cancellation_reasons','id')],
            'note' => ['required_without:reason_id', 'string', 'max:512']
        ];
    }

}
