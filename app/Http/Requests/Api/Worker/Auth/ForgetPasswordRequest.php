<?php

namespace App\Http\Requests\Api\Customer\Auth;


use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Rules\KSAPhoneRule;

class ForgetPasswordRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    protected function prepareForValidation() {
        if (!$this->filled('country_code')) {
            $this->merge(['country_code' => "966"]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'phone' => ['required', new KSAPhoneRule, 'exists:users'],
        ];
    }

    public function currentUser() {
        return User::where('phone', $this->get("phone"))->firstOrFail();
    }


}
