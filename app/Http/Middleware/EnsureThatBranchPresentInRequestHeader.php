<?php

namespace App\Http\Middleware;

use Api;
use Closure;
use Illuminate\Http\Request;
use App\Exceptions\APIException;
use App\Models\Branch;

class EnsureThatBranchPresentInRequestHeader {

    public function handle(Request $request, Closure $next) {
        if (!$request->hasHeader('x-branch-id') || !Branch::where('id', $request->header('x-branch-id'))->exists()) {
            return Api::isError(__("x-branch-id header is missing"));

        }
        return $next($request);
    }
}
