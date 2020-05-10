<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFirebaseTokenDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firebase_token_devices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('member_id');
            $table->string('registration_token');
            $table->string('topic')->default('news');
            $table->text('metadata');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('firebase_token_devices');
    }
}
