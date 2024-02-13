<?php

namespace Netto\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Netto\Traits\HasDefaultAttribute;

/**
 * @property Collection $roles
 */

class Price extends Model
{
    use HasDefaultAttribute;

    public $timestamps = false;
    public $table = 'cms__prices';

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected $attributes = [
        'is_default' => false,
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

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'cms__price__role', 'price_id', 'role_id');
    }
}
