<?php

namespace App\Actions\Authentication;


use Lorisleiva\Actions\Action;

class VerifyAltPhoneAction extends Action {
    public function __construct($request) {
        auth()->user()->update(['phone' => $request->get('phone')]);
        auth()->user()->verificationCodes()->delete();
    }

}
