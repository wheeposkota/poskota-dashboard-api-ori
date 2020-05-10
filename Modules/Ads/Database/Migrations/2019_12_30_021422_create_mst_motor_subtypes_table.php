<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstMotorSubtypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_motor_subtypes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mst_motor_subtype_name');
            $table->unsignedInteger('mst_motor_id');
            $table->timestamps();
        });

        Schema::table('mst_motor_subtypes', function (Blueprint $table) {
            $table->foreign('mst_motor_id')->references('id')->on('mst_motors')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_motor_subtypes');
    }
}
