<?php

namespace Netto\Models\Abstract;

use Netto\Models\Abstract\Pivot as BaseModel;
use App\Models\Section as ParentModel;

abstract class SectionLang extends BaseModel
{
    public $timestamps = false;
    public $table = 'cms_store__sections__lang';

    protected string $parentClass = ParentModel::class;
}
