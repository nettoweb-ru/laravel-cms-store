<?php

namespace Netto\Models;

use Netto\Models\Abstract\Model as BaseModel;
use Netto\Traits\{HasDefaultAttribute, IsMultiLingual};

class OrderStatus extends BaseModel
{
    use HasDefaultAttribute, IsMultiLingual;

    public $timestamps = false;
    public $table = 'cms_store__order_statuses';

    public array $multiLingual = [
        'name',
    ];

    public string $multiLingualClass = OrderStatusLang::class;

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected $attributes = [
        'is_default' => '0',
    ];
}
