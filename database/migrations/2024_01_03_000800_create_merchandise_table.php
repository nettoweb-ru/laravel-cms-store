<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'cms_store__merchandise';

    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('sort')->default(0);
            $table->string('slug')->unique();
            $table->enum('is_active', ['0', '1'])->default('0');
            $table->unsignedMediumInteger('width')->default(0);
            $table->unsignedMediumInteger('length')->default(0);
            $table->unsignedMediumInteger('height')->default(0);
            $table->unsignedMediumInteger('weight')->default(0);
            $table->unsignedBigInteger('album_id')->nullable()->default(null);
            $table->string('thumb')->nullable()->default(null);
            $table->string('photo')->nullable()->default(null);
            $table->foreign('album_id')->references('id')->on('cms__photo_albums')->onDelete('set null')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable(self::TABLE)) {
            Schema::table(self::TABLE, function(Blueprint $table) {
                $table->dropForeign(self::TABLE.'_album_id_foreign');
            });

            Schema::drop(self::TABLE);
        }
    }
};
