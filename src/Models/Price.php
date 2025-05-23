<?php

namespace Netto\Models;

use Netto\Models\Abstract\Model as BaseModel;
use Netto\Traits\{HasAccessCheck, HasDefaultAttribute, IsMultiLingual};

class Price extends BaseModel
{
    use HasDefaultAttribute, IsMultiLingual, HasAccessCheck;

    public $timestamps = false;
    public $table = 'cms_store__prices';

    public array $multiLingual = [
        'name',
    ];

    public string $multiLingualClass = PriceLang::class;
    public string $permissionsTable = 'cms_store__prices__permissions';

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected $attributes = [
        'is_default' => '0',
        'sort' => 0,
    ];
}
