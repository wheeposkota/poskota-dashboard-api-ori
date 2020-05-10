<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstCarSubtypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_car_subtypes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mst_subtype_name');
            $table->unsignedInteger('mst_car_id');
            $table->timestamps();
        });

        Schema::table('mst_car_subtypes', function (Blueprint $table) {
            $table->foreign('mst_car_id')->references('id')->on('mst_cars')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_car_subtypes');
    }
}
