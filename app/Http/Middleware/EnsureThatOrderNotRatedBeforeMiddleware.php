<?php

namespace App\Http\Middleware;

use Api;
use Closure;
use Illuminate\Http\Request;

class EnsureThatOrderNotRatedBeforeMiddleware {

    public function handle(Request $request, Closure $next) {
        if ($request->route('order')->rate()->exists()) {
            return Api::isError(__('validation.api.order_already_rated'));
        }
        return $next($request);
    }
}
