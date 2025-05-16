<?php

namespace App\Actions\Order;

use App\Enum\OrderStatus;
use App\Models\Address;
use App\Models\Catalog\Product;
use App\Models\Order;
use App\Models\OrderOption;
use App\Models\OrderOptionValue;
use App\Models\OrderProduct;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;
use Str;


class CreateAdminOrderAction
{
    use AsAction;

    protected $data = [];

    public function handle($cart, $request)
    {
        $worker = $cart->getWorker();
        $zone = $cart->getZone();
        $slot =$cart->getSlot();

        $type = Product::find($request['product_id'])?->service_type;
        $order = Order::create([
            "status" => OrderStatus::PROCESSING,
            'customer_id' => $request['customer_id'],
            'product_id' => $request['product_id'],
            'worker_id' => $worker['id'],
            'total' => $cart->totals()['items_total_without_options'],
            'date' => $request['date'],
            'from' => $slot['from'],
            'to' => $slot['to'],
            'notes' => $request['notes'],
            'order_type' => $type,
            'zone_id' => $zone['id'],
            'address_id' => $request['address_id'],
            'data' => $cart->totals(),
        ]);
        $adds = $request['adds'] ?? [];
        if (count($adds) > 0) {
            foreach ($adds as $product) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $product,
                ]);
            }
        }

        $options = $request['options'] ?? [];

        if (is_array($options) && count($options) > 0) {
            foreach ($options as $option) {
                $newOption = OrderOption::create([
                    'option_id' => $option['option_id'],
                    'order_id' => $order->id,
                ]);

                if (is_array($option['answer']) && count($option['answer']) > 0) {
                    foreach ($option['answer'] as $value) {
                        OrderOptionValue::create([
                            'order_option_id' => $newOption->id,
                            'value' => $value,
                        ]);
                    }
                } else {
                    OrderOptionValue::create([
                        'order_option_id' => $newOption->id,
                        'value' => $option['answer'],
                    ]);
                }
            }

        }

        return $order;
    }
}
