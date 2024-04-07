<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Netto\Models\Cost;
use Netto\Models\Price;

/**
 * @property Collection $groups
 * @property Collection $costs
 */

class Merchandise extends Model
{
    protected $table = 'cms__merchandise';

    protected $attributes = [
        'sort' => 0,
        'slug' => null,
        'is_active' => '0',
        'width' => 0,
        'length' => 0,
        'height' => 0,
        'weight' => 0,
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'cms__merchandise_group', 'merchandise_id', 'group_id');
    }

    /**
     * @return BelongsToMany
     */
    public function costs(): BelongsToMany
    {
        return $this->belongsToMany(Price::class, Cost::class)->withPivot('currency_id', 'value')->using(Cost::class);
    }
}
