<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrxAdEndDateTrxAdsWebTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trx_ads_web', function (Blueprint $table) {
            $table->datetime('trx_ad_end_date')->after('trx_ad_publish_date')->nullable();
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
            $table->dropColumn(['trx_ad_end_date']);
        });
    }
}
