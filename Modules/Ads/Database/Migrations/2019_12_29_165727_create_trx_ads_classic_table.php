<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrxAdsClassicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_ads_classic', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longtext('trx_ad_content');
            $table->unsignedInteger('mst_ad_type_id');
            $table->unsignedInteger('mst_city_id');
            $table->unsignedBigInteger('rlt_trx_ads_category_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('modified_by');
            $table->timestamps();
        });

        Schema::table('trx_ads_classic', function($table){
            $table->foreign('order_id')->references('id')->on('trx_orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('mst_ad_type_id')->references('id')->on('mst_ad_types')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('mst_city_id')->references('id')->on('mst_cities')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('rlt_trx_ads_category_id')->references('id_rlt_ads_categories')->on('rlt_ads_categories')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('trx_ads_classic');
    }
}
