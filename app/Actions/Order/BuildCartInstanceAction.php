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
use App\Models\ZoneWorker;
use Carbon\Carbon;
use Darryldecode\Cart\Exceptions\InvalidConditionException;
use Lorisleiva\Actions\Concerns\AsAction;

class BuildCartInstanceAction
{
    use AsAction;
    protected $data = [];

    /**
     * @throws InvalidConditionException
     */
    public function handle($data): Cart
    {
        $cart = app('cart');
        $cart->clear();

        $product = $this->applyProductToCart($cart, $data['service_id'], $data['date']);
        $address = $this->getValidatedAddress(request()->get('address_id'));
        $zone = $this->findZoneByLocation($address->location);
        $cart->setZone($zone);

        $worker = $this->findAvailableWorker($zone, $data['date'], $data['from'], $data['to']);
        $cart->setWorker($worker);

        $cart->setSlot($data['from'], $data['to']);

        $walletBalance = CalculateBalanceThatDeductedFromWalletAction::run(auth()->user(), $cart->getTotal());
        $this->applyWalletAndCoupon($cart, $walletBalance, request()->get('coupon_code'));

        if (request()->get('products')) {
            $this->applyAddsToCart($cart, request()->get('products'));
        }

        if (request()->get('options')) {
            $this->applyOptionsToCart($cart, request()->get('options'));
        }

        $this->applyDeposit($cart, $product->deposit);

        return $cart;
    }

    private function applyProductToCart(Cart $cart, $productId, $date): Product
    {
        $product = Product::findOrFail($productId);
        $cart->applyItem($product, number_format($product->price,2));
        $cart->setDate($date);
        return $product;
    }

    private function getValidatedAddress($addressId): Address
    {
        $address = Address::findOrFail($addressId);

        if (!$address->location || !isset($address->location['lat'], $address->location['lng'])) {
            throw new APIException(__('Address location is missing.'));
        }

        return $address;
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
            throw new APIException(__('No zone found for this address.'));
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
        $workers = ZoneWorker::where('zone_id', $zone->id)
            ->where('day', $dayName)
            ->where('status', true)
            ->get();

        foreach ($workers as $worker) {
            $zoneDay = ZoneWorker::where('worker_id', $worker->worker_id)
                ->where('zone_id', $zone->id)
                ->where('day', $dayName)
                ->where('status', true)
                ->first();
            if (!$zoneDay) continue;

            $start = substr($zoneDay['from'], 0, 5);
            $end = substr($zoneDay['to'], 0, 5);


            $workStart = Carbon::createFromFormat('H:i', $start)->setDateFrom($date);
            $workEnd = Carbon::createFromFormat('H:i', $end)->setDateFrom($date);

            if ($slotStart->lt($workStart) && $slotEnd->lt($workStart)) continue;
            if ($slotStart->gt($workEnd) && $slotEnd->gt($workEnd)) continue;

            $availableWorkers[] = ['worker' => $worker->worker];
        }

        if (empty($availableWorkers)) {
            throw new APIException(__('No worker available for this time slot.'));
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
            throw new APIException(__('No worker available for this time slot.'));
        }

        if (count($filtered) === 1) {
            return $filtered[0]['worker'];
        }

        return $filtered->sortBy('orders_count')->first()['worker'];
    }


    private function workerHasConflict(Worker $worker, Carbon $date, $from, $to): bool
    {
        return Order::where('worker_id', $worker->id)
            ->whereDate('date', $date)
            ->paid()
            ->where('status', '!=', 'cancelled')
            ->where('from', $from)
            ->where('to', $to)
            ->exists();
    }

    private function applyWalletAndCoupon(Cart $cart, $walletBalance, $couponCode): void
    {
        $cart->applyWalletDiscount($walletBalance);
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
        $options = request()->get('options', []);
        if (!empty($options)) {
            $mappedOptions = collect($options)->map(function ($option) {
                $optionModel = Option::find($option['option_id']);

                if ($optionModel->type->value === 'radio' || $optionModel->type->value === 'checkbox') {
                    $values = $optionModel->values->whereIn('id', $option['value'])->pluck('value', 'id');
                    return ['id' => $optionModel->id, 'name' => $optionModel->name, 'value' => $values ,'type' => $optionModel->type->value];
                }

                if (in_array($optionModel->type->value, ['textarea', 'date'])) {
                    return ['id' => $optionModel->id, 'name' => $optionModel->name, 'value' => $option['value'], 'type' => $optionModel->type->value];
                }

                return null;
            })->filter();
        }

        $cart->setOptions($mappedOptions);
    }

    private function applyDeposit(Cart $cart, $deposit): void
    {
        $cart->applyDeposit($deposit);
    }
}