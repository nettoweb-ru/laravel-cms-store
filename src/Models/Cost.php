<?php

namespace Netto\Models;

use App\Models\Merchandise;
use Illuminate\Database\Eloquent\Relations\{HasOne, Pivot};

/**
 * @property Merchandise $parent
 */

class Cost extends Pivot
{
    public $timestamps = false;
    public $table = 'cms_store__costs';

    protected $attributes = [
        'value' => 0
    ];

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        self::updated(function(Cost $model): void {
            $model->parent->touch();
        });
    }

    /**
     * @return HasOne
     */
    public function parent(): HasOne
    {
        return $this->hasOne(Merchandise::class, 'id', 'merchandise_id');
    }
}
