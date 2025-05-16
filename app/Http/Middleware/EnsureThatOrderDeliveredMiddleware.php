<?php

namespace App\Http\Middleware;

use Api;
use Closure;
use Illuminate\Http\Request;
use App\Enum\OrderStatus;

class EnsureThatOrderDeliveredMiddleware {

    public function handle(Request $request, Closure $next) {
        if ($request->route('order')->status->value != OrderStatus::COMPLETED->value) {
            return Api::isError(__('validation.api.order_not_delivered_yet'));
        }
        return $next($request);
    }
}
