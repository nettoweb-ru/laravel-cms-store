<?php

namespace Netto\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Merchandise;

/**
 * @property ?Merchandise $merchandise
 * @property Currency $currency
 * @property Cart $cart
 */

class CartItem extends Model
{
    public $timestamps = false;
    public $table = 'cms__cart_items';

    public $attributes = [
        'cost' => '0.00',
        'price' => '0.00',
        'quantity' => 1,
    ];

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
