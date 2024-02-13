<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Netto\Events\OrderStatusChanged;
use Netto\Models\Cart;
use Netto\Models\Currency;
use Netto\Models\OrderHistory;
use Netto\Models\OrderStatus;
use Netto\Services\OrderStatusService;

/**
 * @property OrderStatus $status
 * @property Currency $currency
 * @property Cart $cart
 * @property Collection $items
 * @property Collection $history
 * @property ?User $user
 */

class Order extends Model
{
    public $timestamps = false;
    public $table = 'cms__orders';

    public $casts = [
        'is_locked' => 'boolean',
    ];

    public $attributes = [
        'is_locked' => '0',
        'status_id' => null,
        'total' => '0.00',
        'weight' => 0,
        'volume' => '0.00000000',
    ];

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        self::creating(function(Order $model) {
            $model->setAttribute('created_at', date('Y-m-d H:i:s'));
            $model->checkLocked();
        });

        self::updating(function(Order $model) {
            $model->checkLocked();
        });

        self::saved(function(Order $model) {
            if ($model->original['status_id'] != $model->status_id) {
                $sourceCode = null;
                $targetCode = null;

                foreach (OrderStatusService::getList() as $code => $status) {
                    if ($status['id'] == $model->original['status_id']) {
                        $sourceCode = $code;
                    } elseif ($status['id'] == $model->status_id) {
                        $targetCode = $code;
                    }
                }

                OrderStatusChanged::dispatch($model, $sourceCode, $targetCode);

                $history = new OrderHistory();
                $history->setAttribute('order_id', $model->id);
                $history->setAttribute('status_id', $model->status_id);

                if (Auth::check()) {
                    /** @var User $user */
                    $user = Auth::user();
                    $history->setAttribute('user_id', $user->id);
                }

                $history->save();
            }
        });
    }

    private function checkLocked(): void
    {
        foreach (OrderStatusService::getList() as $status) {
            if (($status['id'] == $this->status_id) && $status['is_final']) {
                $this->setAttribute('is_locked', '1');
                break;
            }
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
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasOne
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class, 'order_id');
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->cart->items();
    }

    /**
     * @return HasMany
     */
    public function history(): HasMany
    {
        return $this->hasMany(OrderHistory::class)->orderBy('created_at')->with(['status', 'user']);
    }
}
