<?php

namespace App\Http\Requests\Api\Customer;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\KSAPhoneRule;

class ContactUsRequest extends FormRequest {

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
            "message" => ["required",],
            'user_id' => [],
        ];
    }

    protected function prepareForValidation() {

        $this->merge([
            'user_id' => !$this->boolean('guest') ? request()->user('sanctum')?->id : null,
        ]);
    }
}
