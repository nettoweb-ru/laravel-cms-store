<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'cms_store__deliveries__permissions';
    private const COLUMNS = [
        'object_id' => 'cms_store__deliveries',
        'related_id' => 'cms__permissions',
    ];

    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            foreach (self::COLUMNS as $columnName => $tableName) {
                $table->unsignedBigInteger($columnName);
                $table->foreign($columnName)->references('id')->on($tableName)->onDelete('cascade')->onUpdate('cascade');
            }

            $table->unique(array_keys(self::COLUMNS));
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable(self::TABLE)) {
            Schema::table(self::TABLE, function(Blueprint $table) {
                foreach (array_reverse(self::COLUMNS) as $columnName => $tableName) {
                    $table->dropForeign(self::TABLE.'_'.$columnName.'_foreign');
                }
            });

            Schema::drop(self::TABLE);
        }
    }
};
