<?php

namespace App\Http\Requests\Api\Customer\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\KSAPhoneRule;

class NewPhoneRequest extends FormRequest {

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
            'phone' => ['required',new KSAPhoneRule()],
            'code' => ['required','numeric','digits:5'],
            'old_phone' => 'required',
        ];
    }
}
