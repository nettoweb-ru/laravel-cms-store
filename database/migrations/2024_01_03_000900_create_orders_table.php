<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'cms__orders';
    private const UNSIGNED_DECIMAL = [
        'total',
        'volume',
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
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->enum('is_locked', ['0', '1'])->default('0');
            $table->decimal('volume', 16,8)->default('0.00000000');
            $table->unsignedBigInteger('weight')->default(0);
            $table->foreign('currency_id')->references('id')->on('cms__currencies')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('status_id')->references('id')->on('cms__order_statuses')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
