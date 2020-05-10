<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdsPathToTrxWebAds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trx_ads_web', function (Blueprint $table) {
            $table->text('path_ads')->after('trx_ad_publish_date');
            if (Schema::hasColumn('trx_ads_web', 'trx_ad_content'))
            {
                $table->dropColumn('trx_ad_content');
            }
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
            $table->text('trx_ad_content')->after('trx_ad_publish_date');
            if (Schema::hasColumn('trx_ads_web', 'path_ads'))
            {
                $table->dropColumn('path_ads');
            }
        });
    }
}
