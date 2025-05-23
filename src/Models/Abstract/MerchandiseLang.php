<?php

namespace Netto\Models\Abstract;

use Netto\Models\Abstract\Pivot as BaseModel;
use App\Models\Merchandise as ParentModel;

abstract class MerchandiseLang extends BaseModel
{
    public $timestamps = false;
    public $table = 'cms_store__merchandise__lang';

    protected string $parentClass = ParentModel::class;
}
