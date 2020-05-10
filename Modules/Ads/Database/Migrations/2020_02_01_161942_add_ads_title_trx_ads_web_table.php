<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdsTitleTrxAdsWebTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trx_ads_web', function (Blueprint $table) {
            $table->string('ads_title')->after(\Modules\Ads\Entities\TrxAdsWeb::getPrimaryKey());
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
            $table->dropColumn(['ads_title']);
        });
    }
}
