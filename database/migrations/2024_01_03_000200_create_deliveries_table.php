<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'cms__deliveries';
    private const UNSIGNED_DECIMAL = [
        'cost',
        'total_min',
        'total_max',
        'volume_min',
        'volume_max',
    ];

    /**
     * @return void
     */
    public function up(): void
    {
        $tableName = self::TABLE;

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort')->default(0);
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('is_active', ['0', '1'])->default('0');
            $table->decimal('cost')->default('0.00');
            $table->decimal('total_min')->default('0.00');
            $table->decimal('total_max')->default('0.00');
            $table->unsignedBigInteger('weight_min')->default(0);
            $table->unsignedBigInteger('weight_max')->default(0);
            $table->decimal('volume_min', 16,8)->default('0.00000000');
            $table->decimal('volume_max', 16,8)->default('0.00000000');
            $table->unsignedBigInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('cms__currencies')->onDelete('restrict')->onUpdate('cascade');
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
