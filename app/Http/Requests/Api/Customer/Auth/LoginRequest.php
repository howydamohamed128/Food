<?php

namespace App\Http\Requests\Api\Customer\Auth;

use App\Rules\CheckPhoneFormat;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\KSAPhoneRule;

class LoginRequest extends FormRequest {
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
            'phone' => ['required', new KSAPhoneRule()]
        ];
    }


}
