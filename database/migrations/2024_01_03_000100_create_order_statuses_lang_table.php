<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    private const TABLE = 'cms_store__order_statuses__lang';
    private const COLUMNS = [
        'object_id' => 'cms_store__order_statuses',
        'lang_id' => 'cms__lang',
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
            $table->string('name');
        });

        $default = DB::table('cms_store__order_statuses')->select('id')->where('is_default', '1')->get()->get(0);
        $language = DB::table('cms__lang')->select('id')->where('is_default', '1')->get()->get(0);

        DB::table(self::TABLE)->insert([
            ['object_id' => $default->id, 'lang_id' => $language->id, 'name' => 'Новый'],
        ]);
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
