<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdTaxonomyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_taxonomy', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('term_id');
            $table->text('description')->nullable();
            $table->string('taxonomy');
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->timestamps();
        });

        Schema::table('ad_taxonomy', function($table){
            $table->foreign('term_id')->references(\Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\Terms::getPrimaryKey())->on('terms')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('parent_id')->references('id')->on('ad_taxonomy')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('category_id')->references('id')->on('mst_ad_categories')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_taxonomy');
    }
}
