<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'cms__merchandise_lang';

    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->unsignedBigInteger('merchandise_id');
            $table->unsignedBigInteger('lang_id');
            $table->string('title');
            $table->longText('content')->nullable()->default(null);
            $table->string('meta_title')->nullable()->default(null);
            $table->text('meta_description')->nullable()->default(null);
            $table->text('meta_keywords')->nullable()->default(null);
            $table->string('og_title')->nullable()->default(null);
            $table->text('og_description')->nullable()->default(null);

            $table->unique(['merchandise_id', 'lang_id']);
            $table->foreign('merchandise_id')->references('id')->on('cms__merchandise')->onDelete('cascade')->onUpdate('cascade');
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
