<?php

namespace App\Http\Middleware;

use Api;
use Closure;
use Illuminate\Http\Request;

class EnsureThatOrderNotCanceledBeforeMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next) {
        if ($request->route('order')->status == \App\Enum\OrderStatus::CANCELED) {
            return Api::isError(__('validation.api.order_already_canceled'));

        }
        return $next($request);
    }
}
