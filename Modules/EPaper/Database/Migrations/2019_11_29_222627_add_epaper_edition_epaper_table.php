<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEpaperEditionEpaperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('electronic_paper', function (Blueprint $table) {
            $table->date('epaper_edition')->after('epaper_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('electronic_paper', function (Blueprint $table) {
            if (Schema::hasColumn('electronic_paper', 'epaper_edition'))
            {
                $table->dropColumn('epaper_edition');
            }
        });
    }
}
