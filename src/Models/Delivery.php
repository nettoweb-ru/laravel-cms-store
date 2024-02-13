<?php

namespace Netto\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Netto\Traits\HasMultiLangAttributes;

/**
 * @property Currency $currency
 * @property Collection $roles
 */

class Delivery extends Model
{
    use HasMultiLangAttributes;

    public $timestamps = false;
    public $table = 'cms__deliveries';

    public $attributes = [
        'sort' => 0,
        'cost' => '0.00',
        'is_active' => false,
        'total_min' => '0.00',
        'total_max' => '0.00',
        'weight_min' => 0,
        'weight_max' => 0,
        'volume_min' => '0.00000000',
        'volume_max' => '0.00000000',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected array $multiLang = [
        'title',
        'description',
    ];

    protected string $multiLangClass = DeliveryLang::class;

    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'cms__delivery__role', 'delivery_id', 'role_id');
    }
}
