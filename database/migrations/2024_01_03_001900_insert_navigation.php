<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const TABLE_GROUPS = 'cms__navigation_groups';
    private const TABLE_NAVIGATION_PERMISSIONS = 'cms__navigation__permissions';

    /**
     * @return void
     */
    public function up(): void
    {
        DB::table(self::TABLE_GROUPS)->insert([
            ['sort' => 20, 'is_system' => '1', 'name' => 'main.navigation_group_store'],
        ]);

        $groups = [];
        foreach (DB::table(self::TABLE_GROUPS)->get() as $item) {
            $groups[$item->name] = $item->id;
        }

        DB::table('cms__navigation')->insert([
            ['group_id' => $groups['main.navigation_group_store'], 'is_system' => '1', 'sort' => 10, 'name' => 'main.list_merchandise', 'url' => 'admin.store.merchandise.index', 'highlight' => '["admin.store.section.edit", "admin.store.section.create", "admin.store.merchandise.edit", "admin.store.merchandise.create"]'],
            ['group_id' => $groups['main.navigation_group_store'], 'is_system' => '1', 'sort' => 20, 'name' => 'main.list_order', 'url' => 'admin.store.order.index', 'highlight' => '["admin.store.order.edit", "admin.store.order.create", "admin.store.status.edit", "admin.store.status.create"]'],
            ['group_id' => $groups['main.navigation_group_store'], 'is_system' => '1', 'sort' => 30, 'name' => 'main.list_price', 'url' => 'admin.store.price.index', 'highlight' => '["admin.store.price.edit", "admin.store.price.create"]'],
            ['group_id' => $groups['main.navigation_group_store'], 'is_system' => '1', 'sort' => 40, 'name' => 'main.list_delivery', 'url' => 'admin.store.delivery.index', 'highlight' => '["admin.store.delivery.edit", "admin.store.delivery.create"]'],
        ]);

        $permissions = [];
        foreach (DB::table('cms__permissions')->get() as $item) {
            $permissions[$item->slug] = $item->id;
        }

        $items = [];
        foreach (DB::table('cms__navigation')->get() as $item) {
            $items[$item->url] = $item->id;
        }

        DB::table(self::TABLE_NAVIGATION_PERMISSIONS)->insert([
            ['object_id' => $items['admin.store.merchandise.index'], 'related_id' => $permissions['admin-store-merchandise']],
            ['object_id' => $items['admin.store.order.index'], 'related_id' => $permissions['admin-store-orders']],
            ['object_id' => $items['admin.store.price.index'], 'related_id' => $permissions['admin-store-prices']],
            ['object_id' => $items['admin.store.delivery.index'], 'related_id' => $permissions['admin-store-deliveries']],
        ]);
    }

    /**
     * @return void
     */
    public function down(): void
    {
        DB::table(self::TABLE_GROUPS)->where('name', 'main.navigation_group_store')->delete();
    }
};
