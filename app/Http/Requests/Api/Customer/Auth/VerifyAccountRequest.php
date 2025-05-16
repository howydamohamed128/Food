<?php

namespace App\Http\Requests\Api\Customer\Auth;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Rules\IsValidVerificationCodeRule;
use App\Rules\KSAPhoneRule;

class VerifyAccountRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'phone' => ['required', 'exists:users',new KSAPhoneRule()],

            'code' => ['required', 'numeric','digits:5', new IsValidVerificationCodeRule()],
        ];
    }

    public function currentUser() {
        return Customer::where('phone', $this->get("phone"))->firstOrFail();
    }
}
