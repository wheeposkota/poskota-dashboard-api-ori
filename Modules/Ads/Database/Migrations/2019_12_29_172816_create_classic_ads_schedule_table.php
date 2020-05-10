<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassicAdsScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classic_ads_schedule', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('trx_ad_publish_date');
            $table->unsignedBigInteger('trx_ad_classic_id');
            $table->timestamps();
        });

        Schema::table('classic_ads_schedule', function($table){
            $table->foreign('trx_ad_classic_id')->references('id')->on('trx_ads_classic')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classic_ads_schedule');
    }
}
