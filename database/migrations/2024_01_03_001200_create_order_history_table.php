<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'cms__order_history';

    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->dateTime('created_at');

            $table->foreign('order_id')->references('id')->on('cms__orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('status_id')->references('id')->on('cms__order_statuses')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
