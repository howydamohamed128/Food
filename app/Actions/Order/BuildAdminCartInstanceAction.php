<?php

namespace App\Actions\Order;

use App\Actions\User\CalculateBalanceThatDeductedFromWalletAction;
use App\Exceptions\APIException;
use App\Lib\Cart;
use App\Models\Add;
use App\Models\Address;
use App\Models\Catalog\Product;
use App\Models\Option;
use App\Models\Order;
use App\Models\Worker;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;
use Filament\Notifications\Notification as FilamentNotification;

class BuildAdminCartInstanceAction
{
    use AsAction;

    public function handle(array $data): Cart
    {
        $cart = app('cart');
        $cart->clear();

        $product = Product::findOrFail($data['product_id']);
        $cart->applyItem($product, $product->price);
        $cart->setDate($data['date']);

        $address = Address::findOrFail($data['address_id']);
        if (!$address->location || !isset($address->location['lat'], $address->location['lng'])) {
            FilamentNotification::make()
                ->danger()
                ->title(__('panel.messages.error'))
                ->body(__('panel.messages.location_missing'))
                ->persistent()
                ->send();
            throw ValidationException::withMessages([
                'custom_error' => __('panel.messages.location_missing'),
            ]);
        }

        $zone = $this->findZoneByLocation($address->location);
        $cart->setZone($zone);

        $from = $data['slot'];
        $to = Carbon::parse("{$data['date']} {$from}")
            ->addMinutes((int) $product->implementation_periods)
            ->format('H:i');

        $worker = $this->findAvailableWorker($zone, $data['date'], $from, $to);
        $cart->setWorker($worker);

        $cart->setSlot($from, $to);

        // $walletBalance = CalculateBalanceThatDeductedFromWalletAction::run(auth()->user(), $cart->getTotal());
        $this->applyWalletAndCoupon($cart, $data['coupon_code'] ?? null);

        if (!empty($data['adds'])) {
            $this->applyAddsToCart($cart, $data['adds']);
        }

        if (!empty($data['options'])) {
            $this->applyOptionsToCart($cart, $data['options']);
        }

        $cart->applyDeposit(0);

        return $cart;
    }

    private function findZoneByLocation(array $location): Zone
    {
        $lat = $location['lat'];
        $lng = $location['lng'];

        $zone = Zone::query()
            ->selectRaw('*, ST_Distance_Sphere(location, POINT(?, ?)) as distance', [$lng, $lat])
            ->whereRaw('ST_Contains(boundaries, ST_GeomFromText(?))', ["POINT($lng $lat)"])
            ->orderBy('distance')
            ->first();

        if (!$zone) {
            FilamentNotification::make()
                ->danger()
                ->title(__('panel.messages.error'))
                ->body(__('panel.messages.zone_no_found'))
                ->persistent()
                ->send();
            throw ValidationException::withMessages([
                'custom_error' => __('panel.messages.zone_no_found'),
            ]);
        }

        return $zone;
    }

    private function findAvailableWorker(Zone $zone, $dateString, $from, $to): Worker
    {
        $date = Carbon::parse($dateString);
        $slotStart = Carbon::parse("$dateString $from");
        $slotEnd = Carbon::parse("$dateString $to");
        $dayName = strtolower($date->format('l'));

        $availableWorkers = [];

        foreach ($zone->workers as $worker) {
            $workingDays = $worker->working_days[0] ?? [];
            $daySettings = $workingDays[$dayName] ?? null;

            if (!$daySettings || !$daySettings['status']) continue;

            $workStart = Carbon::createFromFormat('H:i', $daySettings['from'])->setDateFrom($date);
            $workEnd = Carbon::createFromFormat('H:i', $daySettings['to'])->setDateFrom($date);

            if ($slotStart->lt($workStart) && $slotEnd->lt($workStart)) continue;
            if ($slotStart->gt($workEnd) && $slotEnd->gt($workEnd)) continue;

            $availableWorkers[] = ['worker' => $worker];
        }

        if (empty($availableWorkers)) {
            FilamentNotification::make()
                ->danger()
                ->title(__('panel.messages.error'))
                ->body(__('panel.messages.no_worker_available'))
                ->persistent()
                ->send();
            throw ValidationException::withMessages([
                'custom_error' => __('panel.messages.no_worker_available'),
            ]);
        }

        $filtered = collect($availableWorkers)->map(function ($worker) use ($date, $from, $to) {
            $ordersCount = Order::where('worker_id', $worker['worker']->id)
                ->whereDate('date', $date)
                ->paid()
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($from, $to) {
                    $query->where('from', '<', $to)->where('to', '>', $from);
                })
                ->count();

            return ['worker' => $worker['worker'], 'orders_count' => $ordersCount];
        })->filter(function ($worker) use ($date, $from, $to) {
            return !Order::where('worker_id', $worker['worker']->id)
                ->whereDate('date', $date)
                ->paid()
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($from, $to) {
                    $query->where('from', '<', $to)->where('to', '>', $from);
                })
                ->exists();
        });

        if ($filtered->isEmpty()) {
            FilamentNotification::make()
                ->danger()
                ->title(__('panel.messages.error'))
                ->body(__('panel.messages.no_worker_available'))
                ->persistent()
                ->send();
            throw ValidationException::withMessages([
                'custom_error' => __('panel.messages.no_worker_available'),
            ]);
        }

        return $filtered->sortBy('orders_count')->first()['worker'];
    }

    private function applyWalletAndCoupon(Cart $cart, $couponCode): void
    {
        // $cart->applyWalletDiscount($walletBalance);
        $cart->applyCoupon($couponCode);
    }

    private function applyAddsToCart(Cart $cart, array $productIds): void
    {
        $adds = Add::whereIn('id', $productIds)->get();
        $price = $adds->sum('price');
        $cart->applyProducts($price);
        $cart->setAdd($adds);
    }

    private function applyOptionsToCart(Cart $cart, array $options): void
    {
        $mappedOptions = collect($options)->map(function ($option) {
            $optionModel = Option::find($option['option_id']);
            if (!$optionModel) return null;

            if ($optionModel->type->value === 'radio' || $optionModel->type->value === 'checkbox') {
                $values = $optionModel->values
                    ->whereIn('id', (array) $option['answer'])
                    ->pluck('value', 'id');
                return [
                    'id' => $optionModel->id,
                    'name' => $optionModel->name,
                    'value' => $values,
                ];
            }

            if (in_array($optionModel->type->value, ['textarea', 'date'])) {
                return [
                    'id' => $optionModel->id,
                    'name' => $optionModel->name,
                    'value' => $option['answer'],
                ];
            }

            return null;
        })->filter();

        $cart->setOptions($mappedOptions);
    }
}
