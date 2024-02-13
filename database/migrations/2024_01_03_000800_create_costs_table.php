<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'cms__costs';
    private const UNSIGNED_DECIMAL = [
        'value',
    ];

    /**
     * @return void
     */
    public function up(): void
    {
        $tableName = self::TABLE;

        Schema::create($tableName, function (Blueprint $table) {
            $table->unsignedBigInteger('price_id');
            $table->unsignedBigInteger('merchandise_id');
            $table->unsignedBigInteger('currency_id');
            $table->decimal('value')->default(0);
            $table->foreign('price_id')->references('id')->on('cms__prices')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('merchandise_id')->references('id')->on('cms__merchandise')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('currency_id')->references('id')->on('cms__currencies')->onDelete('cascade')->onUpdate('cascade');
            $table->primary(['price_id', 'merchandise_id', 'currency_id']);
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
        Schema::dropIfExists(self::TABLE);
    }
};
