<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property ?Group $parent
 */

class Group extends Model
{
    public $table = 'cms__groups';

    protected $attributes = [
        'sort' => 0,
        'parent_id' => null,
        'is_active' => '0',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
