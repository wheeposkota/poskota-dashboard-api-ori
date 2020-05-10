<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxonomyRelationshipAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxonomy_relationship_ads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ad_taxonomy_id');
            $table->unsignedBigInteger('object_id')->unique();
            $table->integer('term_order')->default(0);
            $table->timestamps();
        });

        Schema::table('taxonomy_relationship_ads', function($table){
            $table->foreign('ad_taxonomy_id')->references('id')->on('ad_taxonomy')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('object_id')->references('id')->on('trx_ads_classic')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxonomy_relationship_ads');
    }
}
