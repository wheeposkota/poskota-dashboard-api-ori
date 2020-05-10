<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrxOrdersMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_orders_meta', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('trx_order_id');
            $table->string('meta_key');
            $table->longtext('meta_value')->nullable();
            $table->timestamps();
        });

         Schema::table('trx_orders_meta', function (Blueprint $table) {
            $table->foreign('trx_order_id')->references('id')->on('trx_orders')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trx_orders_meta');
    }
}
