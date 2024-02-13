<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Netto\Models\GroupLang;
use Netto\Traits\HasMultiLangAttributes;

/**
 * @property ?Group $parent
 */

class Group extends Model
{
    use HasMultiLangAttributes;

    public $table = 'cms__groups';

    protected $attributes = [
        'sort' => 0,
        'parent_id' => null,
        'is_active' => false,
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

    protected string $multiLangClass = GroupLang::class;

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
