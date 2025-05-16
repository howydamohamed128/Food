<?php

namespace App\Actions\User;

use App\Exceptions\APIException;
use App\Models\Coupon;
use Lorisleiva\Actions\Concerns\AsAction;

class ApplyCouponAction {
    use AsAction;

    /**
     * @throws APIException
     */
    public function handle($code) {
        $coupon = Coupon::where('code', $code)->first();
        $coupon->applyForUser(auth()->user());
        return $coupon;
    }

}
