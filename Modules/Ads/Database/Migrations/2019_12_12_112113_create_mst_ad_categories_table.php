<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstAdCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_ad_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('mst_ad_cat_name', 150);
            $table->string('mst_ad_cat_slug')->unique();
            $table->unsignedBigInteger('mst_parent_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('modified_by');
            $table->timestamps();
        });

        Schema::table('mst_ad_categories', function($table){
            $table->foreign('mst_parent_id')->references('id')->on('mst_ad_categories')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('mst_ad_categories');
    }
}
