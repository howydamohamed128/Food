<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Catalog\Product;
use App\Models\Zone;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AvailableSlotsService
{
    public function get(int $productId, string $date, int $addressId): array
    {
        $address = Address::find($addressId);
        $product = Product::find($productId);

        if (!$address || !$product || !$address->location) {
            return [];
        }

        $lat = $address->location['lat'];
        $lng = $address->location['lng'];

        $zone = Zone::query()
            ->selectRaw('*, ST_Distance_Sphere(location, POINT(?, ?)) as distance', [$lng, $lat])
            ->whereRaw('ST_Contains(boundaries, ST_GeomFromText(?))', ["POINT($lng $lat)"])
            ->orderBy('distance')
            ->first();

        if (!$zone) {
            return [];
        }

        $dayName = strtolower(Carbon::parse($date)->format('l'));
        $duration = (int) $product->implementation_periods;

        $slots = collect();

        foreach ($zone->workers as $worker) {
            $workingDays = $worker->working_days[0] ?? [];
            $settings = $workingDays[$dayName] ?? null;

            if (!$settings || !$settings['status']) {
                continue;
            }

            $start = Carbon::createFromFormat('H:i', $settings['from'])->setDateFrom(Carbon::parse($date));
            $end = Carbon::createFromFormat('H:i', $settings['to'])->setDateFrom(Carbon::parse($date));

            $period = CarbonPeriod::create($start, "$duration minutes", $end->copy()->subMinutes($duration));

            foreach ($period as $slotStart) {
                $slotEnd = $slotStart->copy()->addMinutes($duration);

                $isReserved = $worker->orders()
                    ->paid()
                    ->where('status', '!=', 'cancelled')
                    ->whereDate('date', $date)
                    ->get()
                    ->some(function ($order) use ($slotStart, $slotEnd) {
                        $dateOnly = Carbon::parse($order->date)->format('Y-m-d');

                        $orderStart = Carbon::parse($dateOnly . ' ' . $order->from);
                        $orderEnd = Carbon::parse($dateOnly . ' ' . $order->to);

                        return $slotStart->lt($orderEnd) && $slotEnd->gt($orderStart);
                    });

                if (!$isReserved) {
                    $slots->push([
                        'from' => $slotStart->format('H:i'),
                        'to' => $slotEnd->format('H:i'),
                    ]);
                }
            }
        }

        return $slots->unique(fn($slot) => $slot['from'])->values()->all();
    }
}
