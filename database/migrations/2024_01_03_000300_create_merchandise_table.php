<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'cms__merchandise';

    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort')->default(0);
            $table->string('name')->nullable()->default(null);
            $table->string('slug')->unique();
            $table->enum('is_active', ['0', '1'])->default('0');

            $table->unsignedBigInteger('width')->default(0);
            $table->unsignedBigInteger('length')->default(0);
            $table->unsignedBigInteger('height')->default(0);
            $table->unsignedBigInteger('weight')->default(0);

            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE);
    }
};
