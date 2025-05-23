<?php

namespace Netto\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use App\Models\Order;

/**
 * @property ?Order $order
 * @property Collection $items
 */

class Cart extends BaseModel
{
    public $timestamps = false;
    public $table = 'cms_store__carts';

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class)->orderBy('id')->with('merchandise');
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        $return = 0;
        $currencyCode = find_currency_code($this->getAttribute('currency_id'));

        foreach ($this->items->all() as $item) {
            /** @var CartItem $item */
            if ($item->getAttribute('currency_id') == $this->getAttribute('currency_id')) {
                $return += $item->getAttribute('cost');
            } else {
                $return += convert_currency(
                    $item->getAttribute('cost'),
                    find_currency_code($item->getAttribute('currency_id')),
                    $currencyCode
                );
            }
        }

        return $return;
    }

    /**
     * @return float
     */
    public function getVolume(): float
    {
        $return = 0;
        foreach ($this->items->all() as $item) {
            /** @var CartItem $item */
            $return += $item->merchandise->getAttribute('width') * $item->merchandise->getAttribute('length') * $item->merchandise->getAttribute('height') * $item->getAttribute('quantity') / 1000000000;
        }

        return $return;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        $return = 0;
        foreach ($this->items->all() as $item) {
            /** @var CartItem $item */
            $return += $item->merchandise->getAttribute('weight') * $item->getAttribute('quantity');
        }

        return $return;
    }
}
