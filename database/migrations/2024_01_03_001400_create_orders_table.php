<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    private const TABLE = 'cms_store__orders';
    private const UNSIGNED_DECIMAL = [
        'total',
        'volume',
        'delivery_cost',
    ];

    /**
     * @return void
     */
    public function up(): void
    {
        $tableName = self::TABLE;

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->dateTime('created_at');
            $table->decimal('total')->default('0.00');
            $table->unsignedBigInteger('currency_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('delivery_id');
            $table->decimal('delivery_cost')->default('0.00');
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->decimal('volume', 16,8)->default('0.00000000');
            $table->unsignedBigInteger('weight')->default(0);
            $table->foreign('currency_id')->references('id')->on('cms_currency__currencies')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('status_id')->references('id')->on('cms_store__order_statuses')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('delivery_id')->references('id')->on('cms_store__deliveries')->onDelete('restrict')->onUpdate('cascade');
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
                $table->dropForeign(self::TABLE.'_delivery_id_foreign');
                $table->dropForeign(self::TABLE.'_user_id_foreign');
                $table->dropForeign(self::TABLE.'_status_id_foreign');
                $table->dropForeign(self::TABLE.'_currency_id_foreign');
            });

            Schema::drop(self::TABLE);
        }
    }
};
