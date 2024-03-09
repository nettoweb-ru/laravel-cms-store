<?php

namespace Netto\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DeliveryLang extends Pivot
{
    public $timestamps = false;
    public $table = 'cms__delivery_lang';
}
