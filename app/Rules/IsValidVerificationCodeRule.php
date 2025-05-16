<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\VerificationCode;

class IsValidVerificationCodeRule implements Rule {
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        return VerificationCode::where('code', $value)->where("phone", request()->get('phone'))->where('expired_at', ">=", now())->count();

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return __('validation.api.invalid_verification_code');
    }
}
