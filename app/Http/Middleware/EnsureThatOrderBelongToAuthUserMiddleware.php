<?php

namespace App\Http\Middleware;

use Api;
use Closure;
use Illuminate\Http\Request;

class EnsureThatOrderBelongToAuthUserMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next) {
        if ($request->route('order')->customer_id != auth()->id()) {
            return Api::setMessage('):')->setStatus(401);
        }
        return $next($request);
    }
}
