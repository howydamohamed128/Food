<?php

namespace App\Actions\Order;

use App\Exceptions\APIException;
use App\Models\Address;
use App\Models\Catalog\Product;
use App\Models\Zone;
use App\Models\ZoneWorker;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetAvailableSlotsAction
{
    use AsAction;

    public function handle(array $data): array
    {
        $address = $this->getAddressWithLocation($data['address_id']);
        $zone = $this->findZoneByLocation($address->location['lat'], $address->location['lng']);
        $date = Carbon::parse($data['date']);
        $product = $this->getProductWithValidDuration($data['service_id']);

        $dayName = strtolower($date->format('l'));
        $slotDuration = (int) $product->implementation_periods;

        $allAvailableSlots = collect();

        $workers = ZoneWorker::where('zone_id', $zone->id)
            ->where('day', $dayName)
            ->where('status', true)
            ->get();

        if (!empty($workers)) {
            foreach ($workers as $worker) {
                $daySettings = $this->getWorkerDaySettings($worker, $dayName, $zone->id);

                if (!$daySettings) {
                    continue;
                }

                $slots = $this->generateAvailableSlotsForWorker($worker->worker, $daySettings, $date, $slotDuration);

                $allAvailableSlots = $allAvailableSlots->merge($slots);
            }
        }

        $uniqueSlots = $allAvailableSlots->unique(fn($slot) => $slot['from'] . '-' . $slot['to']);

        return $uniqueSlots->values()->all();
    }

    protected function getAddressWithLocation(int $addressId): Address
    {
        $address = Address::findOrFail($addressId);

        if (!$address->location || !isset($address->location['lat'], $address->location['lng'])) {
            throw new APIException(__('Address location is not set'));
        }

        return $address;
    }

    protected function findZoneByLocation(float $lat, float $lng): Zone
    {
        // البحث في المضلعات المتعددة
        $zone = Zone::query()
            ->selectRaw('*, ST_Distance_Sphere(location, POINT(?, ?)) as distance', [$lng, $lat])
            ->whereRaw('ST_Contains(boundaries, ST_GeomFromText(?))', ["POINT($lng $lat)"])
            ->orderBy('distance')
            ->first(); // الحصول على أقرب منطقة بناءً على المسافة

        // في حال عدم العثور على المنطقة
        if (!$zone) {
            throw new APIException(__('Zone not found'));
        }

        // إرجاع المنطقة
        return $zone;
    }


    protected function getProductWithValidDuration(int $productId): Product
    {
        $product = Product::findOrFail($productId);

        if ((int) $product->implementation_periods <= 0) {
            throw new APIException(__('Invalid slot duration'));
        }

        return $product;
    }

    // protected function getWorkerDaySettings($worker, string $dayName): ?array
    // {
    //     $workingDays = $worker->working_days[0] ?? [];
    //     $daySettings = $workingDays[$dayName] ?? null;

    //     return ($daySettings && $daySettings['status']) ? $daySettings : null;
    // }

    protected function getWorkerDaySettings($worker, string $dayName, int $zoneId): ?array
    {
        $zoneDay = ZoneWorker::where('worker_id', $worker->worker_id)
            ->where('zone_id', $zoneId)
            ->where('day', $dayName)
            ->where('status', true)
            ->first();
        if (!$zoneDay) {
            return null;
        }

        return [
            'from' => $zoneDay->from,
            'to' => $zoneDay->to,
        ];
    }


    protected function generateAvailableSlotsForWorker($worker, array $daySettings, Carbon $date, int $slotDuration): array
    {
        $from = substr($daySettings['from'], 0, 5);
        $to = substr($daySettings['to'], 0, 5);

        $start = Carbon::createFromFormat('H:i', $from)->setDateFrom($date);
        $end = Carbon::createFromFormat('H:i', $to)->setDateFrom($date);
        // dd($start, $end);

        if ($start->gte($end)) {
            return [];
        }

        $timeSlots = CarbonPeriod::create($start, "$slotDuration minutes", $end->copy()->subMinutes($slotDuration));

        // dd($worker);
        $reservations = $worker->orders()
            ->paid()
            ->where('status', '!=', 'cancelled')
            ->whereDate('date', $date->toDateString())
            ->get();

        $available = [];

        foreach ($timeSlots as $slotStart) {
            $slotEnd = $slotStart->copy()->addMinutes($slotDuration);

            if (!$this->isSlotReserved($slotStart, $slotEnd, $reservations)) {
                $available[] = [
                    'from' => $slotStart->format('H:i'),
                    'to'   => $slotEnd->format('H:i'),
                ];
            }
        }

        return $available;
    }

    protected function isSlotReserved(Carbon $slotStart, Carbon $slotEnd, $reservations): bool
    {
        foreach ($reservations as $reservation) {
            $reservationDate = Carbon::parse($reservation->date);
            $reservedStart = $reservationDate->copy()->setTimeFrom(Carbon::parse($reservation->from));
            $reservedEnd   = $reservationDate->copy()->setTimeFrom(Carbon::parse($reservation->to));

            if (
                $slotStart->lt($reservedEnd) &&
                $slotEnd->gt($reservedStart)
            ) {
                return true;
            }
        }

        return false;
    }
}
