<?php

namespace Netto\Models\Abstract;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};
use Illuminate\Support\Facades\{Auth, DB};
use Netto\Events\OrderStatusChanged;
use Netto\Models\{Cart, CartItem, Currency, Delivery, OrderHistory, OrderStatus};

use Netto\Models\Abstract\Model as BaseModel;

/**
 * @property Cart $cart
 * @property ?Delivery $delivery
 * @property Currency $currency
 * @property Collection $history
 * @property OrderStatus $status
 * @property ?User $user
 */

abstract class Order extends BaseModel
{
    public $timestamps = false;
    public $table = 'cms_store__orders';

    public $attributes = [
        'total' => '0.00',
        'delivery_cost' => '0.00',
        'weight' => 0,
        'volume' => '0.00000000',
    ];

    public array $cartData = [];
    public array $cartSaveData = [];

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        self::created(function(Order $model): void {
            if (empty($model->cart)) {
                $cart = new Cart();
                $cart->setAttribute('order_id', $model->getAttribute('id'));
                $cart->save();

                $model->setRelation('cart', $cart);
            }
        });

        self::saving(function(Order $model): void {
            if (!$model->exists) {
                $model->setAttribute('created_at', date('Y-m-d H:i:s'));
            }

            $model->prepareCartData();
        });

        self::saved(function(Order $model): void {
            $model->saveCartData();
            $model->saveTotals();
            $model->saveHistory();
        });
    }

    /**
     * @return HasOne
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class, 'order_id');
    }

    /**
     * @return BelongsTo
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * @return HasMany
     */
    public function history(): HasMany
    {
        return $this->hasMany(OrderHistory::class)->orderBy('created_at')->with(['status', 'user']);
    }

    /**
     * @return void
     */
    public function loadCart(): void
    {
        if (!$this->exists) {
            return;
        }

        foreach ($this->cart->items->all() as $item) {
            $this->cartData[$item->id] = [
                'merchandise_id' => $item->merchandise_id,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'cost' => $item->cost,
            ];
        }
    }

    /**
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return void
     */
    protected function prepareCartData(): void
    {
        foreach ($this->getAttributes() as $attribute => $value) {
            if (str_starts_with($attribute, 'cart|')) {
                if (str_ends_with($attribute, 'merchandise_id')) {
                    $tmp = explode('|', $attribute);
                    if (is_null($value)) {
                        $this->cartSaveData[] = [
                            'type' => 'delete',
                            'id' => $tmp[1],
                        ];
                    } else {
                        $this->cartSaveData[] = [
                            'type' => 'update',
                            'id' => $tmp[1],
                            'merchandise_id' => $value,
                            'price' => $this->getAttribute("{$tmp[0]}|{$tmp[1]}|price"),
                            'quantity' => $this->getAttribute("{$tmp[0]}|{$tmp[1]}|quantity"),
                        ];
                    }
                }

                unset($this->{$attribute});
            }

            if (str_starts_with($attribute, 'cart_new|')) {
                if (str_ends_with($attribute, 'merchandise_id') && !is_null($value)) {
                    $tmp = explode('|', $attribute);
                    $this->cartSaveData[] = [
                        'type' => 'add',
                        'merchandise_id' => $value,
                        'price' => $this->getAttribute("{$tmp[0]}|{$tmp[1]}|price"),
                        'quantity' => $this->getAttribute("{$tmp[0]}|{$tmp[1]}|quantity"),
                    ];
                }

                unset($this->{$attribute});
            }
        }
    }

    /**
     * @return void
     */
    protected function saveCartData(): void
    {
        if (empty($this->cartSaveData)) {
            return;
        }

        $deleteId = [];
        $cartItems = $this->cart->items->all();

        foreach ($this->cartSaveData as $item) {
            $type = $item['type'];
            unset($item['type']);

            switch ($type) {
                case 'add':
                    $object = new CartItem();
                    $object->setAttribute('cart_id', $this->cart->getAttribute('id'));
                    $object->setAttribute('currency_id', $this->getAttribute('currency_id'));

                    foreach ($item as $attribute => $value) {
                        $object->setAttribute($attribute, $value);
                    }

                    $object->save();
                    break;
                case 'delete':
                    $deleteId[] = $item['id'];
                    break;
                case 'update':
                    $object = null;
                    $id = $item['id'];
                    unset($item['id']);

                    foreach ($cartItems as $cartItem) {
                        /** @var CartItem $cartItem */
                        if ($cartItem->getAttribute('id') == $id) {
                            $object = $cartItem;
                            break;
                        }
                    }

                    if ($object) {
                        foreach ($item as $attribute => $value) {
                            $object->setAttribute($attribute, $value);
                        }

                        $object->save();
                    }
                    break;
            }
        }

        if ($deleteId) {
            CartItem::query()->whereIn('id', $deleteId)->delete();
        }
    }

    /**
     * @return void
     */
    protected function saveHistory(): void
    {
        $changes = $this->getChanges();
        $statusId = null;

        if (array_key_exists('status_id', $changes)) {
            $statusId = $changes['status_id'];
        } else if ($this->wasRecentlyCreated) {
            $statusId = get_default_order_status_id();
        }

        if ($statusId) {
            $object = new OrderHistory();
            $object->setAttribute('order_id', $this->getAttribute('id'));
            $object->setAttribute('status_id', $statusId);

            if (Auth::check()) {
                /** @var User $user */
                $user = Auth::getUser();
                $object->setAttribute('user_id', $user->getAttribute('id'));
            }

            $object->save();

            foreach (get_order_status_list() as $slug => $status) {
                if ($status['id'] == $statusId) {
                    OrderStatusChanged::dispatch($this, $slug);
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function saveTotals(): void
    {
        $this->cart->refresh();

        $total = $this->cart->getTotal() + $this->getAttribute('delivery_cost');
        $volume = $this->cart->getVolume();
        $weight = $this->cart->getWeight();

        DB::table($this->getTable())->where('id', $this->getAttribute('id'))->update([
            'total' => $total,
            'volume' => $volume,
            'weight' => $weight,
        ]);

        $this->setAttribute('total', $total);
        $this->setAttribute('volume', $volume);
        $this->setAttribute('weight', $weight);
    }
}
