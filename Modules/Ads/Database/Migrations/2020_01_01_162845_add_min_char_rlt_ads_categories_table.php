<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMinCharRltAdsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rlt_ads_categories', function (Blueprint $table) {
            $table->unsignedInteger('min_char')->default(0)->after('category_id');
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
            $table->dropColumn(['min_char']);
        });
    }
}
