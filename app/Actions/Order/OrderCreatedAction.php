<?php

namespace App\Actions\Order;

use App\Notifications\Lib\NotificationsSender;
use App\Orders\Models\Order;
use App\Orders\Notifications\NewOrderNotification;
use App\Utilities\Lib\Action;

class OrderCreatedAction extends Action {

    public function __construct(Order $order) {
        $order->decreaseItemsQtyIfStockEnabled();
        new NotificationsSender(
            'orders',
            "new_order",
            new NewOrderNotification($order)
        );
    }
}
