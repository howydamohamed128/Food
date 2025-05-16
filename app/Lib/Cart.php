<?php

namespace App\Lib;

use App\Enum\CustomerTypeStatuses;
use App\Models\Add;
use App\Models\Catalog\Product;
use App\Models\Coupon;
use App\Models\Location\District;
use App\Models\Worker;
use App\Models\Zone;
use App\Settings\GeneralSettings;
use Cknow\Money\Money;
use Darryldecode\Cart\Cart as CoreCart;
use Darryldecode\Cart\CartCondition;
use Darryldecode\Cart\Helpers\Helpers;
use Darryldecode\Cart\ItemCollection;
use DB;

class Cart extends CoreCart
{
    private $orderId;
    private Worker|null $worker = null;
    private $date;
    private $interval;
    private $slot;
    private $from;
    private $to;
    private $zone;
    private $adds;
    private $options;


    public function getSession()
    {
        return $this->sessionKey;
    }

    public function getQuantityByModelId($id): int
    {
        return $this->getContent()->where('associatedModel.id', $id)->first()->quantity ?? 0;
    }


    function removeCartCoupon()
    {
        $this->removeConditionsByType("coupon");
    }

    function applyItem(Product $product, $price)
    {

        $this->add(
            md5($product->id),
            $product->title,
            $price,
            1,
            [
                "original_price" => $price,

            ],
            [],
            $product
        );
    }

    function applyCoupon($code): bool
    {

        !$this->getConditionsByType("coupon")->count() ?: $this->removeConditionsByType("coupon");
        $coupon = Coupon::where('code', trim($code))->first();
        if (!$coupon) {
            return false;
        }
        if ($this->totals()['subtotal'] == 0) {
            return false;
        }
        $coupon_value = $coupon->formattedValue();
        $conditionData = [
            'name' => $coupon->code,
            'type' => "coupon",
            'target' => "subtotal",
            'value' => "-" . $coupon_value,
            'order' => 1,
            'attributes' => [
                'original_value' => "-" . $coupon_value,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    function applyDeliveryService($distance): bool
    {

        $settings = new GeneralSettings();
        $overflowDistance = $distance - $settings->diameter;
        $cost = $settings->delivery_cost;
        if ($overflowDistance > 0) {
            $cost += $overflowDistance * $settings->delivery_cost_for_each_additional_kilometer;
        }
        $conditionData = [
            'name' => 'Delivery service',
            'type' => "delivery",
            'target' => "total",
            'value' => $cost,
            'order' => 2,
            'attributes' => [
                'original_value' => $settings->delivery_cost,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    function applyTakeawayDiscount(): bool
    {

        $settings = new GeneralSettings();
        if (!$settings->enable_orders_discount_upon_receipt_from_the_branch) {
            return false;
        }
        $conditionData = [
            'name' => 'discount upon receipt from the branch',
            'type' => "takeaway",
            'target' => "subtotal",
            'value' => -$settings->orders_discount_upon_receipt_from_the_branch,
            'order' => 2,
            'attributes' => [
                'original_value' => $settings->orders_discount_upon_receipt_from_the_branch,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }


    function applyTaxes()
    {
        $settings = new GeneralSettings();
        $value = $settings->taxes;
        $value = "{$value}%";
        !$this->getConditionsByType("taxes")->count() ?: $this->removeConditionsByType("taxes");
        $conditionData = [
            'name' => 'Taxes',
            'type' => "taxes",
            'target' => "total",
            'value' => $value,
            'order' => 1,
            'attributes' => [
                'original_value' => $value,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    function applyDelivery($type, $districtCost = null): bool
    {
        $districtCost ??= 0;
        $settings = new GeneralSettings();
        $value = match ($type) {

            'super_delivery' => $settings->immediate_delivery_fees ?? 0,
            'delivery' => (int)($districtCost > 0 ? $districtCost : $settings->standard_delivery_fees ?? 0),
            default => 0
        };
        !$this->getConditionsByType("delivery")->count() ?: $this->removeConditionsByType("delivery");
        $conditionData = [
            'name' => 'Delivery',
            'type' => "delivery",
            'target' => "total",
            'value' => $value,
            'order' => 2,
            'attributes' => [
                'original_value' => $value,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    function applyCashOnDeliveryCost(): bool
    {
        $settings = new GeneralSettings();
        $value = $settings->payment_fee_upon_receipt ?? 0;
        !$this->getConditionsByType("cash_on_delivery_cost")->count() ?: $this->removeConditionsByType("cash_on_delivery_cost");

        $conditionData = [
            'name' => 'cash_on_delivery_cost',
            'type' => "cash_on_delivery_cost",
            'target' => "total",
            'value' => $value,
            'order' => 2,
            'attributes' => [
                'original_value' => $value,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    function applyWalletDiscount(string $discount): bool
    {
        !$this->getConditionsByType("wallet")->count() ?: $this->removeConditionsByType("wallet");
        $conditionData = [
            'name' => 'wallet',
            'type' => "wallet",
            'target' => "subtotal",
            'value' => -$discount,
            'order' => 1,
            'attributes' => [
                'original_value' => $discount,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    function applyProducts(string $discount): bool
    {
        !$this->getConditionsByType("adds")->count() ?: $this->removeConditionsByType("adds");
        $conditionData = [
            'name' => 'adds',
            'type' => "adds",
            'target' => "total",
            'value' => $discount,
            'order' => 1,
            'attributes' => [
                'original_value' => $discount,
            ]
        ];
        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }
    function applyDeposit(string $deposit): bool
    {
        !$this->getConditionsByType("deposit")->count() ?: $this->removeConditionsByType("deposit");
        $deposit = $deposit . '%';
        $total = $this->getTotal();
        $deposit_value = $total * (float)$deposit / 100;
        $conditionData = [
            'name' => 'deposit',
            'type' => "deposit",
            'target' => "",
            'value' => $deposit_value,
            'order' => 1,
            'attributes' => [
                'original_value' => $deposit_value,
            ]
        ];

        $conditionData['attributes'] = $conditionData;
        $this->condition(new CartCondition($conditionData));
        return true;
    }

    public function setOrderConditions(): static
    {
        foreach ($this->getConditions() as $condition) {
            DB::table('orders_conditions')->insert([
                'order_id' => $this->getOrderID(),
                'name' => $condition->getName(),
                'type' => $condition->getType(),
                'target' => $condition->getTarget(),
                'value' => $condition->getValue(),
                'order' => $condition->getOrder(),
                'attributes' => json_encode($condition->getAttributes()),
                'model' => null,
            ]);
            if ($condition->getType() == 'coupon') {
                $coupon = Coupon::where('code', $condition->getName())->first();
                $coupon->users()->attach([auth()->id() => [
                    'order_id' => $this->getOrderID(),
                ]]);
            }
        }


        return $this;
    }

    public function getOrderItemConditions($item): array
    {
        $conditions = [];
        foreach ($item->getConditions() as $condition) {
            $conditions[] = [

                'name' => $condition->getName(),
                'type' => $condition->getType(),
                'target' => $condition->getTarget(),
                'value' => $condition->getValue(),
                'order' => $condition->getOrder(),
                'attributes' => json_encode($condition->getAttributes()),
                'model' => null,
            ];
        }
        return $conditions;
    }

    public function nativeItems()
    {
        $ids = [];
        foreach ($this->getContent() as $item) {
            $ids[] = $item['associatedModel']->id;
        }
        return Items::whereIn('id', array_unique($ids))->get();
    }

    private function setOrderItemsLine(): static
    {
        /** @var ItemCollection $item */
        foreach ($this->getContent() as $item) {
            $model = collect($item->associatedModel)->only([
                "id",
                "title",
                "status",
                "price",
                "image",
            ]);
            DB::table('orders_items_lines')->insert([
                'order_id' => $this->getOrderID(),
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'attributes' => json_encode($item->attributes),
                'conditions' => json_encode($this->getOrderItemConditions($item)),
                'model' => json_encode($model),
            ]);
        }

        return $this;
    }

    public function saveItemsToOrderAndClearAll($orderID)
    {
        $this->saveItemsToOrder($orderID);
        parent::clearCartConditions();
        parent::clear();
    }

    public function saveItemsToOrder($orderID)
    {
        $this->setOrderID($orderID)
            ->setOrderItemsLine()
            ->setOrderConditions();
    }


    public function setOrderID($orderID): static
    {
        $this->orderId = $orderID;
        return $this;
    }

    public function getOrderID()
    {
        return $this->orderId;
    }

    public function foramtedTotal()
    {
        return $this->getTotal() . ' ' . Ecommerce::currentSymbol();
    }

    public function getWorker(): Worker|null
    {
        return $this->worker;
    }

    public function setWorker(Worker $worker): void
    {
        $this->worker = $worker;
    }
    public function getZone(): Zone|null
    {
        return $this->zone;
    }

    public function setZone(Zone $zone): void
    {
        $this->zone = $zone;
    }

    public function getAdd()
    {
        return $this->adds;
    }

    public function setAdd( $adds): void
    {
        $this->adds = $adds;
    }
    public function getoptions()
    {
        return $this->options;
    }

    public function setOptions($options): void
    {
        $this->options   = $options;
    }

    /**
     * @return mixed
     */
    public function getSlot()
    {
        $slot = [
            'from' => $this->from,
            'to' => $this->to,
        ];
        return $slot;
    }

    /**
     * @param mixed $slot
     */
    public function setSlot($from, $to): void
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @param mixed $interval
     */
    public function setInterval($interval): void
    {
        $this->interval = $interval;
    }

    protected function updateQuantityRelative($item, $key, $value)
    {
        if (preg_match('/\-/', $value) == 1) {
            $value = (float)str_replace('-', '', $value);

            // we will not allowed to reduced quantity to 0, so if the given value
            // would result to item quantity of 0, we will not do it.
            if (($item[$key] - $value) > 0) {
                $item[$key] -= $value;
            }
        } elseif (preg_match('/\+/', $value) == 1) {
            $item[$key] += (float)str_replace('+', '', $value);
        } else {
            $item[$key] += (float)$value;
        }

        return $item;
    }

    protected function updateQuantityNotRelative($item, $key, $value)
    {
        $item[$key] = (float)$value;
        return $item;
    }


    public function itemsTotalWithoutVat()
    {
        return $this->getContent()->sum(fn($i) => $i->getPriceSum());
    }

    public function itemsVatTotal()
    {
        $itemsVatTotal = $this->getContent()->sum(function (ItemCollection $item) {
            return collect($item->getConditions())->sum(function ($cond) use ($item) {
                return $cond->getCalculatedValue($item->getPriceSum());
            });
        });
        $config = $this->config;
        $config['format_numbers'] = true;
        return (float)Helpers::formatValue($itemsVatTotal, true, $config);
    }

    function format($value)
    {
        return Money::parse(abs($value))->format();
    }

    public function hasDiscount(): bool
    {
        return $this->getConditionsByType('coupon')->count();
    }

    public function hasCashOnDeliveryFees(): bool
    {
        return $this->getConditionsByType('cash_on_delivery_cost')->count();
    }

    public function hasAdminDiscount(): bool
    {
        return $this->getConditionsByType('discount')->count();
    }

    public function hasWalletDiscount(): bool
    {
        return $this->getConditionsByType('wallet')->count();
    }

    public function discount(): float
    {
        return $this->getConditionsByType('coupon')
            ?->first()?->getCalculatedValue($this->getContent()->sum(fn(ItemCollection $item) => $item->getPriceSumWithConditions(true))) ?? 0;
    }

    public function cashOnDeliveryCost()
    {

        return $this->getConditionsByType('cash_on_delivery_cost')?->first()?->getValue() * 100;
    }

    public function adminDiscount()
    {
        return $this->getConditionsByType('discount')?->first()?->getCalculatedValue($this->getSubTotal());
    }

    public function walletDiscount()
    {
        return $this->getConditionsByType('wallet')?->first()?->getValue();
    }

    public function formattedTotals(): array
    {
        return array_map([$this, 'format'], $this->totals());
    }


    public function totals(): array
    {

        $items_total_with_options = $this->getContent()->sum(fn(ItemCollection $item) => $item->getPriceSumWithConditions(true));
        return [
            'items_total_without_options' => $this->getSubTotalWithoutConditions(),
            "wallet" => abs($this->getConditionsByType("wallet")?->first()?->getValue()),
            "subtotal" =>  $items_total_with_options - abs($this->getConditionsByType("wallet")?->first()?->getValue()),
            "discount" =>($items_total_with_options - abs($this->getConditionsByType("wallet")?->first()?->getValue()) ) - $this->discount(),
            'adds' => $this->getConditionsByType('adds')?->first()?->getValue() ?? 0,
            "total" => $this->getTotal(),
            'deposit' => $this->getConditionsByType('deposit')?->first()?->getValue() ?? 0,
            'remain' =>$this->getTotal() - $this->getConditionsByType('deposit')?->first()?->getValue() ?? 0,
        ];
    }
}
