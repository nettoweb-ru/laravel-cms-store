<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'cms__delivery__role';

    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_id');
            $table->unsignedBigInteger('role_id');
            $table->foreign('delivery_id')->references('id')->on('cms__deliveries')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('role_id')->references('id')->on('cms__roles')->onDelete('cascade')->onUpdate('cascade');
            $table->primary(['delivery_id', 'role_id']);
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
