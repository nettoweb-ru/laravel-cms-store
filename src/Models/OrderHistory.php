<?php

namespace Netto\Models;

use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Order $order
 * @property OrderStatus $status
 */

class OrderHistory extends Model
{
    public $timestamps = false;
    public $table = 'cms__order_history';

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        self::creating(function(OrderHistory $model) {
            $model->created_at = date('Y-m-d H:i:s');
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }
}
