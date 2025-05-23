<?php

namespace Netto\Models;

use Netto\Models\Abstract\Pivot as BaseModel;

class DeliveryLang extends BaseModel
{
    public $timestamps = false;
    public $table = 'cms_store__deliveries__lang';

    protected string $parentClass = Delivery::class;
}
