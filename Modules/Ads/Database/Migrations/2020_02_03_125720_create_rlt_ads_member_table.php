<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRltAdsMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rlt_ads_member', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->unique();
            $table->unsignedBigInteger('member_id');
            $table->timestamps();
        });

        Schema::table('rlt_ads_member', function($table){
            $table->foreign('order_id')->references('id')->on('trx_orders')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('rlt_ads_member');
    }
}
