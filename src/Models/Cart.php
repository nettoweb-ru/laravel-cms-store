<?php

namespace Netto\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Netto\Services\CurrencyService;
use App\Models\Order;

/**
 * @property ?Order $order
 * @property Collection $items
 */

class Cart extends Model
{
    public $timestamps = false;
    public $table = 'cms__carts';

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        self::creating(function(Cart $cart) {
            do {
                $cart->setAttribute('slug', bin2hex(random_bytes(32)));
                if (count(self::where('slug', $cart->slug)->get()) === 0) {
                    break;
                }
            } while (true);
        });
    }

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
        return $this->hasMany(CartItem::class)->orderBy('id')->with(['currency', 'merchandise']);
    }

    /**
     * @return float
     */
    public function getVolume(): float
    {
        $return = 0;
        if (empty($this->items)) {
            return $return;
        }

        foreach ($this->items as $item) {
            /** @var CartItem $item */
            if ($item->merchandise) {
                $return += ($item->merchandise->width * $item->merchandise->length * $item->merchandise->height / 1000000000 * $item->quantity);
            }
        }

        return $return;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        $return = 0;
        if (empty($this->items)) {
            return $return;
        }

        foreach ($this->items as $item) {
            /** @var CartItem $item */
            if ($item->merchandise) {
                $return += ($item->merchandise->weight * $item->quantity);
            }
        }

        return $return;
    }

    /**
     * @param string|null $currencyCode
     * @return float
     */
    public function getTotal(?string $currencyCode = null): float
    {
        $return = 0;
        if (empty($this->items)) {
            return $return;
        }

        if (is_null($currencyCode)) {
            $currencyCode = CurrencyService::getDefaultCode();
        }

        $currencies = CurrencyService::getList();
        if (!array_key_exists($currencyCode, $currencies)) {
            return $return;
        }

        foreach ($this->items as $item) {
            $return += CurrencyService::convertValue($item->cost, $item->currency->slug, $currencyCode, 2);
        }

        return $return;
    }
}
