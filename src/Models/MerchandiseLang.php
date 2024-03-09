<?php

namespace Netto\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MerchandiseLang extends Pivot
{
    public $timestamps = false;
    public $table = 'cms__merchandise_lang';
}
