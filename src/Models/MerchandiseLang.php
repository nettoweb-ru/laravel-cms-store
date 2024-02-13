<?php

namespace Netto\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MerchandiseLang extends Pivot
{
    public const PIVOT_KEY = 'merchandise_id';

    public $timestamps = false;
    public $table = 'cms__merchandise_lang';
}
