<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    private const TABLE = 'cms_store__cart_items';
    private const UNSIGNED_DECIMAL = [
        'price',
        'cost',
    ];

    /**
     * @return void
     */
    public function up(): void
    {
        $tableName = self::TABLE;

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('merchandise_id')->nullable()->default(null);
            $table->unsignedBigInteger('currency_id');
            $table->decimal('price')->default('0.00');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('cost')->default('0.00');
            $table->foreign('cart_id')->references('id')->on('cms_store__carts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('merchandise_id')->references('id')->on('cms_store__merchandise')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('currency_id')->references('id')->on('cms_currency__currencies')->onDelete('restrict')->onUpdate('cascade');
        });

        foreach (self::UNSIGNED_DECIMAL as $item) {
            DB::statement("ALTER TABLE `{$tableName}` ADD CONSTRAINT `{$tableName}_{$item}_check` CHECK (`{$item}` >= 0)");
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable(self::TABLE)) {
            Schema::table(self::TABLE, function(Blueprint $table) {
                $table->dropForeign(self::TABLE.'_currency_id_foreign');
                $table->dropForeign(self::TABLE.'_merchandise_id_foreign');
                $table->dropForeign(self::TABLE.'_cart_id_foreign');
            });

            Schema::drop(self::TABLE);
        }
    }
};
