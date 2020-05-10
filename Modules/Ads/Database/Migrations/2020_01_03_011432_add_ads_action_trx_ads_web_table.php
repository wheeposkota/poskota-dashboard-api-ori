<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdsActionTrxAdsWebTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trx_ads_web', function (Blueprint $table) {
            $table->text('ads_action')->after('path_ads');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trx_ads_web', function (Blueprint $table) {
            $table->dropColumn(['ads_action']);
        });
    }
}
