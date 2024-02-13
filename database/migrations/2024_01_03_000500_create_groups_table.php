<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'cms__groups';

    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort')->default(0);
            $table->string('name')->nullable()->default(null);
            $table->string('slug');
            $table->enum('is_active', ['0', '1'])->default('0');
            $table->unsignedBigInteger('parent_id')->nullable()->default(null);
            $table->foreign('parent_id')->references('id')->on(self::TABLE)->onDelete('set null')->onUpdate('cascade');
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
