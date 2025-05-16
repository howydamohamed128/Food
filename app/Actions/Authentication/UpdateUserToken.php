<?php

namespace App\Actions\Authentication;

use App\Models\Manager;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;


class UpdateUserToken {
    use AsAction;

    public function handle(User|Manager $user): bool {
        $user->tokens()->delete();
        $user->update(['api_token'=>$user->createToken("Tasawk:Token")->plainTextToken,'phone_verified_at' => now()]);
        return true;
    }

}
