<?php

namespace App\Http\Requests\Api\Customer\Profile;

use Illuminate\Foundation\Http\FormRequest;

class ProfileSettingRequest extends FormRequest {

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
            'notification_status' => 'required|in:0,1',
            'preferred_language' => 'required|in:ar,en',
        ];

    }
}
