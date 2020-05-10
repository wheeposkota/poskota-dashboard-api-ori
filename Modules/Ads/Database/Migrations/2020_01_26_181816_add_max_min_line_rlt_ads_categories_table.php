<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaxMinLineRltAdsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rlt_ads_categories', function (Blueprint $table) {
            $table->unsignedInteger('max_char')->default(0)->change();
            $table->unsignedInteger('min_line')->default(0)->after('max_char');
            $table->unsignedInteger('max_line')->default(0)->after('min_line');
            $table->unsignedInteger('char_on_line')->default(0)->after('max_line');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rlt_ads_categories', function (Blueprint $table) {
            $table->dropColumn(['min_line', 'max_line', 'char_on_line']);
        });
    }
}
