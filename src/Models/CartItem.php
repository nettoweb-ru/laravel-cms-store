<?php

namespace Netto\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Merchandise;

/**
 * @property Cart $cart
 * @property Currency $currency
 * @property ?Merchandise $merchandise
 */

class CartItem extends BaseModel
{
    public $timestamps = false;
    public $table = 'cms_store__cart_items';

    public $attributes = [
        'cost' => '0.00',
        'price' => '0.00',
        'quantity' => 1,
    ];

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        self::saving(function(CartItem $model) {
            $model->setAttribute('cost', $model->getAttribute('price') * $model->getAttribute('quantity'));
        });
    }

    /**
     * @return BelongsTo
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * @return BelongsTo
     */
    public function merchandise(): BelongsTo
    {
        return $this->belongsTo(Merchandise::class)->with('costs');
    }
}
