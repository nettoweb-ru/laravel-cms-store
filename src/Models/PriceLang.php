<?php

namespace Netto\Models;

use Netto\Models\Abstract\Pivot as BaseModel;

class PriceLang extends BaseModel
{
    public $timestamps = false;
    public $table = 'cms_store__prices__lang';

    protected string $parentClass = Price::class;
}
