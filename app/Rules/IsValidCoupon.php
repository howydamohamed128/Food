<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Coupon;

class IsValidCoupon implements Rule {
    protected string $message = '';

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
     * @param mixed $valueCheckCoupon
     * @return bool
     */
    public function passes($attribute, $value) {
        $cp = Coupon::where('code', $value)->where('type', 'gift')->first();
        $auth_user = request()->user('sanctum');

        if (!$cp) {
            $this->message = (__("validation.api.coupon_code_not_found"));
            return false;
        }
        if ($cp?->isUserExceedUsageTimes($auth_user)) {
            $this->message = (__("validation.api.coupon_code_exceeds_the_number_of_usages_times"));
            return false;
        }
        if (!$cp->isAvailableToUse()) {
            $this->message = (__("validation.api.coupon_code_is_expired"));
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->message;
    }
}
