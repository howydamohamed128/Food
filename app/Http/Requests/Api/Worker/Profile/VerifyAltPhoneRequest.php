<?php

namespace App\Http\Requests\Api\Customer\Profile;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IsValidVerificationCodeRule;
use App\Rules\KSAPhoneRule;

class VerifyAltPhoneRequest extends FormRequest {

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
            'phone' => ['required', 'exists:verification_codes', new KSAPhoneRule()],
            'code' => ['required', 'numeric','digits:5', new IsValidVerificationCodeRule()],

        ];
    }

}
