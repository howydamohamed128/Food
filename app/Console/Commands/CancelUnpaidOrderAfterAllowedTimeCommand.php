<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Enum\OrderStatus;
use App\Models\Order;

class CancelUnpaidOrderAfterAllowedTimeCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cancel-unpaid-order-after-allowed-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command For cancel orders after allowed time';

    /**
     * Execute the console command.
     */
    public function handle() {
        $orders = Order::where('status', OrderStatus::PROCESSING)->where('date', '<', now()->subMinutes(60))->get();

        foreach ($orders as $order) {
            $order->update(['status' => OrderStatus::CANCELED]);
            $this->info("Order #{$order->id} has been canceled.");
        }
    }
}