<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEpaperSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('epaper_subscription', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->unsignedBigInteger('order_id')->unique();
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('member_id');
        });

        Schema::table('epaper_subscription', function($table){
            $table->foreign('order_id')->references('id')->on('trx_orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('package_id')->references('id')->on('epaper_package')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('epaper_subscription');
    }
}
