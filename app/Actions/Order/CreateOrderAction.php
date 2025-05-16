<?php

namespace App\Actions\Order;

use App\Enum\OrderStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderOption;
use App\Models\OrderOptionValue;
use App\Models\OrderProduct;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;
use Str;


class CreateOrderAction
{
    use AsAction;

    protected $data = [];

    public function handle($cart, $request)
    {
        $worker = $cart->getWorker();
        $zone = $cart->getZone();
        if (request()->get('national_code') != null) {
            $address = Address::where('id', $request['address_id'])->first();
            $address->national_code = $request['national_code'];
            $address->save();
        }

        $order = Order::create([
            "status" => OrderStatus::PROCESSING,
            'customer_id' => auth()->id(),
            'product_id' => $request['service_id'],
            'worker_id' => $worker['id'],
            'total' => $cart->totals()['items_total_without_options'],
            'date' => $request['date'],
            'from' => $request['from'],
            'to' => $request['to'],
            'notes' => $request['notes'],
            'order_type' => $request['type'],
            'zone_id' => $zone['id'],
            'address_id' => $request['address_id'],
            'data' => $cart->totals(),
        ]);

        if (count(request()->get('products')) > 0) {
            foreach (request()->get('products') as $product) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $product,
                ]);
            }
        }

        $options = request()->get('options', []);
        if (is_array($options) && count($options) > 0) {
            foreach ($options as $option) {
                $Newoption = OrderOption::create([
                    'option_id' => $option['option_id'],
                    'order_id' => $order->id,
                ]);


                if (is_array($option['value']) && count($option['value']) > 0) {
                    foreach ($option['value'] as $value) {
                        OrderOptionValue::create([
                            'order_option_id' => $Newoption->id,
                            'value' => $value,
                        ]);
                    }
                } else {
                    OrderOptionValue::create([
                        'order_option_id' => $Newoption->id,
                        'value' => $option['value'],
                    ]);
                }
            }
        }

        return $order;
    }
}
