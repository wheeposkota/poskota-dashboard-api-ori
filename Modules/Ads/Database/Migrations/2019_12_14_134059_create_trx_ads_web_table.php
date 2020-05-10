<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrxAdsWebTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_ads_web', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('trx_ad_publish_date');
            $table->longtext('trx_ad_content');
            $table->unsignedBigInteger('order_id')->unique();
            $table->unsignedInteger('mst_ads_position_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('modified_by');
        });

        Schema::table('trx_ads_web', function($table){
            $table->foreign('order_id')->references('id')->on('trx_orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('mst_ads_position_id')->references('id')->on('mst_ads_position')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trx_ads_web');
    }
}
