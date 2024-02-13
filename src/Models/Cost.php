<?php

namespace Netto\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Cost extends Pivot
{
    public $timestamps = false;
    public $table = 'cms__costs';
}
