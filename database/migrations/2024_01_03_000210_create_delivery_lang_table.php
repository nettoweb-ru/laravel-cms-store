<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'cms__delivery_lang';

    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_id');
            $table->unsignedBigInteger('lang_id');
            $table->string('title');
            $table->longText('description')->nullable()->default(null);

            $table->unique(['delivery_id', 'lang_id']);
            $table->foreign('delivery_id')->references('id')->on('cms__deliveries')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('lang_id')->references('id')->on('cms__languages')->onDelete('cascade')->onUpdate('cascade');
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
