<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'cms_store__sections__merchandise';

    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('merchandise_id');
            $table->foreign('section_id')->references('id')->on('cms_store__sections')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('merchandise_id')->references('id')->on('cms_store__merchandise')->onDelete('cascade')->onUpdate('cascade');
            $table->unique(['section_id', 'merchandise_id'], 'cms_store__sections__merchandise_unique');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable(self::TABLE)) {
            Schema::table(self::TABLE, function(Blueprint $table) {
                $table->dropForeign(self::TABLE.'_merchandise_id_foreign');
                $table->dropForeign(self::TABLE.'_section_id_foreign');
            });

            Schema::drop(self::TABLE);
        }
    }
};
