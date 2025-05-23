<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'cms_store__carts';

    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable()->default(null);
            $table->unsignedBigInteger('currency_id');
            $table->string('slug', 64)->nullable()->default(null);
            $table->dateTime('expires_at')->nullable()->default(null);
            $table->foreign('order_id')->references('id')->on('cms_store__orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('currency_id')->references('id')->on('cms_currency__currencies')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable(self::TABLE)) {
            Schema::table(self::TABLE, function(Blueprint $table) {
                $table->dropForeign(self::TABLE.'_order_id_foreign');
            });

            Schema::drop(self::TABLE);
        }
    }
};
