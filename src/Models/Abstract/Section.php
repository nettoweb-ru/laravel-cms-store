<?php

namespace Netto\Models\Abstract;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};
use Netto\Models\Abstract\Model as BaseModel;
use Netto\Models\Album;
use Netto\Traits\{HasUploads, IsMultiLingual};
use App\Models\{Merchandise, SectionLang};

/**
 * @property Album $album
 * @property Collection $merchandise
 * @property ?\App\Models\Section $parent
 */

abstract class Section extends BaseModel
{
    use HasUploads, IsMultiLingual;

    public array $multiLingual = [
        'name',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
    ];

    public string $multiLingualClass = SectionLang::class;

    public $table = 'cms_store__sections';

    public array $uploads = [
        'photo' => [
            'storage' => 'public',
            'width' => 900,
            'height' => 900,
        ],
        'thumb' => [
            'storage' => 'public',
            'width' => 150,
            'height' => 150,
            'auto' => 'photo',
        ],
    ];

    protected $attributes = [
        'sort' => 0,
        'is_active' => '0',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    /**
     * @return BelongsToMany
     */
    public function merchandise(): BelongsToMany
    {
        return $this->belongsToMany(Merchandise::class, 'cms_store__sections__merchandise', 'section_id', 'merchandise_id');
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }
}
