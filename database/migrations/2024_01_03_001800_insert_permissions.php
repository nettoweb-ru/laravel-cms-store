<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const TABLE_ROLES = 'cms__roles';
    private const TABLE_ROLES_LANG = 'cms__roles__lang';
    private const TABLE_PERMISSIONS = 'cms__permissions';
    private const TABLE_ROLES_PERMISSIONS = 'cms__roles__permissions';

    private const CODES = [
        'admin-store-merchandise' => 'main.list_merchandise',
        'admin-store-orders' => 'main.list_order',
        'admin-store-deliveries' => 'main.list_delivery',
        'admin-store-prices' => 'main.list_price',
    ];
    private const ROLE_CODE = 'store-operator';

    /**
     * @return void
     */
    public function up(): void
    {
        DB::table(self::TABLE_ROLES)->insert([
            'name' => 'main.role_store_operator',
            'slug' => self::ROLE_CODE,
            'is_system' => '1',
        ]);

        foreach (self::CODES as $key => $value) {
            DB::table(self::TABLE_PERMISSIONS)->insert([
                'name' => $value,
                'slug' => $key,
                'is_system' => '1',
            ]);
        }

        $permissions = [];
        foreach (DB::table('cms__permissions')->get() as $item) {
            $permissions[$item->slug] = $item->id;
        }

        $roles = [];
        foreach (DB::table('cms__roles')->get() as $item) {
            $roles[$item->slug] = $item->id;
        }

        DB::table(self::TABLE_ROLES_PERMISSIONS)->insert([
            ['object_id' => $roles['developer'], 'related_id' => $permissions['admin-store-merchandise']],
            ['object_id' => $roles['developer'], 'related_id' => $permissions['admin-store-orders']],
            ['object_id' => $roles['developer'], 'related_id' => $permissions['admin-store-deliveries']],
            ['object_id' => $roles['developer'], 'related_id' => $permissions['admin-store-prices']],
            ['object_id' => $roles[self::ROLE_CODE], 'related_id' => $permissions['admin-store-merchandise']],
            ['object_id' => $roles[self::ROLE_CODE], 'related_id' => $permissions['admin-store-orders']],
            ['object_id' => $roles[self::ROLE_CODE], 'related_id' => $permissions['admin-store-deliveries']],
            ['object_id' => $roles[self::ROLE_CODE], 'related_id' => $permissions['admin-store-prices']],
            ['object_id' => $roles[self::ROLE_CODE], 'related_id' => $permissions['admin-currencies']],
        ]);
    }

    /**
     * @return void
     */
    public function down(): void
    {
        DB::table(self::TABLE_PERMISSIONS)->whereIn('slug', array_keys(self::CODES))->delete();
        DB::table(self::TABLE_ROLES)->where('slug', self::ROLE_CODE)->delete();
    }
};
