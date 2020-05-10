<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_ads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('mst_ad_name', 150);
            $table->string('mst_ad_slug')->unique();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('modified_by');
            $table->timestamps();
        });

        Schema::table('mst_ads', function($table){
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
        Schema::dropIfExists('mst_ads');
    }
}
