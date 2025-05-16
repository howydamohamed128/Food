<?php

namespace App\Console\Commands;

use App\Enum\OrderStatus;
use App\Models\Order;
use App\Notifications\ReservationTimeIsClosestNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class ReminderCustomerThatReservationTimeIsClosestCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder-customer-that-reservation-time-is-closest-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle() {
        Order::where('status', OrderStatus::PROCESSING)
            ->where('date', now()->format('Y-m-d'))
            ->whereHas('slot', fn($record) => $record->where('from', "<", now()->addHours(1)))
            ->chunk(100, function ($reservations) {
                foreach ($reservations as $reservation) {
                    $reservation->save();
                    Notification::send($reservation->customer, new ReservationTimeIsClosestNotification($reservation));
                }
            });
    }
}
