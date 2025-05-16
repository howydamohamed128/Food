<?php

namespace App\Http\Requests\Api\Customer\Auth;

use App\Rules\IsValidVerificationCodeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;
use App\Enum\CustomerTypeStatuses;
use App\Enum\GenderEnum;
use App\Rules\KSAPhoneRule;

class RegisterCustomerRequest extends FormRequest {

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
            'first_name' => ['required', 'string', 'min:3', 'max:40'],
            'last_name' => ['required', 'string', 'min:3', 'max:40'],
            'phone' => [
                'required',
                'unique:users,phone,NULL,id,deleted_at,NULL',
                new KSAPhoneRule()
            ],
            'code' => ['required', 'numeric', 'digits:5',new IsValidVerificationCodeRule()],
            'location' => ['required', 'array'],
            'location.lat' => ['required', 'numeric'],
            'location.lng' => ['required', 'numeric'],
            'email' => ['nullable', 'email', 'unique:users,email,NULL,id,deleted_at,NULL'],
            'national_code' => ['required'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'is_approved_conditions' => ['required', 'in:1'],

        ];
    }
}
