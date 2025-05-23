<?php

namespace Netto\Models;

use Netto\Models\Abstract\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\{User, Order};

/**
 * @property Order $order
 * @property OrderStatus $status
 * @property User $user
 */

class OrderHistory extends BaseModel
{
    public $timestamps = false;
    public $table = 'cms_store__order_history';

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        self::creating(function(OrderHistory $model) {
            $model->setAttribute('created_at', date('Y-m-d H:i:s'));
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
}
