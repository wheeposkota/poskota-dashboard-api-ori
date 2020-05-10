<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRltAdsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rlt_ads_categories', function (Blueprint $table) {
            $table->bigIncrements('id_rlt_ads_categories');
            $table->unsignedBigInteger('ads_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedInteger('max_char');
            $table->decimal('price', 13,0);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('modified_by');
            $table->timestamps();
        });

        Schema::table('rlt_ads_categories', function($table){
            $table->foreign('ads_id')->references('id')->on('mst_ads')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('category_id')->references('id')->on('mst_ad_categories')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('rlt_ads_categories');
    }
}
