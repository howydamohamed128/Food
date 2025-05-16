<?php

namespace App\Actions\User;

use App\Actions\Authentication\SendVerificationCode;
use App\Exceptions\AccountNeedActivationException;
use App\Exceptions\APIException;
use Exception;
use Lorisleiva\Actions\Concerns\AsAction;

class CustomerHasRightsToLogin
{
    use AsAction;


    /**
     * @throws Exception
     */
    public function handle()
    {
        $this->hasRoleCustomer()
            ->isActive()
            ->isPhoneVerified();
    }

    /**
     * @throws Exception
     */
    public function hasRoleCustomer(): CustomerHasRightsToLogin
    {
        if (!auth()->user()->hasRole('customer')) {
            throw new APIException(__('Cant login as customer'));
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function isActive(): CustomerHasRightsToLogin
    {
        if (! auth()->user()->active ) {
            throw new APIException(__('validation.api.account_suspend'));
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function isPhoneVerified(): CustomerHasRightsToLogin
    {
        if (is_null(auth()->user()->phone_verified_at)) {
            SendVerificationCode::run(auth()->user());
            throw new AccountNeedActivationException();
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    //    public function isConfirmed(): CustomerHasRightsToLogin {
    //        if (auth()->user()->admin_confirmed == 0) {
    //            throw new AccountNotApprovedException();
    //        }
    //        return $this;
    //    }

}
