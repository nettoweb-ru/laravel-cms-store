<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Netto\Models\Cost;
use Netto\Models\MerchandiseLang;
use Netto\Models\Price;
use Netto\Traits\HasMultiLangAttributes;

/**
 * @property Collection $groups
 * @property Collection $costs
 */

class Merchandise extends Model
{
    use HasMultiLangAttributes;

    protected $table = 'cms__merchandise';

    protected $attributes = [
        'sort' => 0,
        'slug' => null,
        'is_active' => false,
        'width' => 0,
        'length' => 0,
        'height' => 0,
        'weight' => 0,
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected array $multiLang = [
        'title',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'content',
    ];

    protected string $multiLangClass = MerchandiseLang::class;

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
