<?php

namespace App\Rules;

use App\Enum\ServiceReservationTypesEnum;
use App\Models\Catalog\Product;
use App\Models\Catalog\Service;
use App\Models\Worker;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class WorkerServeServiceRule implements ValidationRule {
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $worker = Worker::find($value);
        $service = Product::find(request()->service_id);
        $interval = request()->get('interval');
        $from = request()->get('slot');
        $to = request()->date('slot')->addMinutes($interval)->format('H:i');
        if ($service->type == ServiceReservationTypesEnum::NOT_TIMED) {
            return;
        }

        if (!$service->workers()->where("worker_id",$worker->id)->exists()) {
            $fail(__("panel.messages.worker_not_serve_this_service"));
            return;
        }

        if (!$service->hasInterval($interval)) {

            $fail(__("panel.messages.invalid_interval"));
            return;
        }

        if (!$worker->isAvailablePeriod(request()->date('date'), $from, $to)) {
            $fail(__("panel.messages.worker_not_available"));
return;
        }

    }
}
