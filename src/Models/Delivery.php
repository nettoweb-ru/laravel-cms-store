<?php

namespace Netto\Models;

use Netto\Models\Abstract\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Netto\Traits\{HasAccessCheck, IsMultiLingual};

/**
 * @property Currency $currency
 */

class Delivery extends BaseModel
{
    use IsMultiLingual, HasAccessCheck;

    public $timestamps = false;
    public $table = 'cms_store__deliveries';

    public $attributes = [
        'sort' => 0,
        'cost' => '0.00',
        'is_active' => '0',
    ];

    public array $multiLingual = [
        'name',
        'description',
    ];

    public string $multiLingualClass = DeliveryLang::class;
    public string $permissionsTable = 'cms_store__deliveries__permissions';

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
