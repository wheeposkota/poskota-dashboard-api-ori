<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceAfterRltCategoryTrxAdsClassicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trx_ads_classic', function (Blueprint $table) {
            $table->decimal('trx_ad_order_price', 13, 0)->after('trx_ad_content');
            $table->unsignedInteger('trx_ad_order_total_char')->after('trx_ad_content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trx_ads_classic', function (Blueprint $table) {
            $table->dropColumn(['trx_ad_order_price', 'trx_ad_order_total_char']);
        });
    }
}
