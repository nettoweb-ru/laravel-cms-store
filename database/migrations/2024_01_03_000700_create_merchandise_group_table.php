<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'cms__merchandise_group';

    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('merchandise_id');
            $table->foreign('group_id')->references('id')->on('cms__groups')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('merchandise_id')->references('id')->on('cms__merchandise')->onDelete('cascade')->onUpdate('cascade');
            $table->primary(['group_id', 'merchandise_id']);
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
