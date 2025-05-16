<?php

namespace App\Http\Requests\Api\Worker\Auth;

use App\Models\Customer;
use App\Models\User;
use App\Models\Worker;
use App\Rules\IsValidVerificationCodeRule;
use App\Rules\KSAPhoneRule;
use Illuminate\Foundation\Http\FormRequest;

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
        return Worker::where('phone', $this->get("phone"))->firstOrFail();
    }
}
