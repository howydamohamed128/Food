<?php

namespace App\Http\Requests\Api\Customer\Profile;

use Arr;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Location\District;
use App\Rules\KSAPhoneRule;

class AddressBookRequest extends FormRequest {

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
            'location.lat' => ['required', 'numeric'],
            'location.lng' => ['required', 'numeric'],
            'national_code' => ['nullable'],
            'title' => ['required','string'],

        ];
    }

}