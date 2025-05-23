<?php

namespace Netto\Models;

use Netto\Models\Abstract\Pivot as BaseModel;

class OrderStatusLang extends BaseModel
{
    public $timestamps = false;
    public $table = 'cms_store__order_statuses__lang';

    protected string $parentClass = OrderStatus::class;
}
