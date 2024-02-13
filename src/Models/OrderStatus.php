<?php

namespace Netto\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Netto\Traits\HasDefaultAttribute;

class OrderStatus extends Model
{
    use HasDefaultAttribute;

    public $timestamps = false;
    public $table = 'cms__order_statuses';

    protected $casts = [
        'is_default' => 'boolean',
        'is_final' => 'boolean',
    ];

    protected $attributes = [
        'is_default' => false,
        'is_final' => false,
    ];

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        self::saved(function($model): void {
            $model->checkSavedDefault();
        });

        self::deleting(function($model): bool {
            return $model->checkDeletingDefault();
        });
    }
}
