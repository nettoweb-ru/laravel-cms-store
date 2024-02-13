<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const TABLE = 'cms__navigation';

    /**
     * @return void
     */
    public function up(): void
    {
        DB::table(self::TABLE)->insert([
            ['group_id' => 10, 'sort' => 10, 'name' => 'cms-store::main.list_merchandise', 'url' => 'admin.group.index', 'highlight' => '["admin.group.index", "admin.group.edit", "admin.group.create", "admin.merchandise.edit", "admin.merchandise.create"]'],
            ['group_id' => 10, 'sort' => 20, 'name' => 'cms-store::main.list_order', 'url' => 'admin.order.index', 'highlight' => '["admin.order.index", "admin.order.edit", "admin.status.edit", "admin.status.create"]'],
            ['group_id' => 10, 'sort' => 30, 'name' => 'cms-store::main.list_price', 'url' => 'admin.price.index', 'highlight' => '["admin.price.index", "admin.price.edit", "admin.price.create"]'],
            ['group_id' => 10, 'sort' => 40, 'name' => 'cms-store::main.list_delivery', 'url' => 'admin.delivery.index', 'highlight' => '["admin.delivery.index", "admin.delivery.edit", "admin.delivery.create"]'],
        ]);
    }

    /**
     * @return void
     */
    public function down(): void
    {
        DB::table(self::TABLE)->whereIn('url', ['admin.group.index', 'admin.order.index', 'admin.price.index', 'admin.delivery.index'])->delete();
    }
};
