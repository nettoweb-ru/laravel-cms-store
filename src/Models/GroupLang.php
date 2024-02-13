<?php

namespace Netto\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupLang extends Pivot
{
    public const PIVOT_KEY = 'group_id';

    public $timestamps = false;
    public $table = 'cms__groups_lang';
}
