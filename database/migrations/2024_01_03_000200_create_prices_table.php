<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    private const TABLE = 'cms_store__prices';

    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('sort')->default(0);
            $table->string('slug')->unique();
            $table->enum('is_default', ['0', '1'])->default('0');
        });

        DB::table(self::TABLE)->insert([
            ['sort' => 10, 'slug' => 'retail', 'is_default' => '1'],
        ]);
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE);
    }
};
