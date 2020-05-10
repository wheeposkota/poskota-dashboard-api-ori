<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrxOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trx_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->decimal('trx_order_quantity', 5, 0);
            $table->decimal('trx_order_price', 13, 0);
            $table->decimal('trx_order_discount', 5, 0)->default(0);
            $table->datetime('trx_order_date');
            $table->unsignedInteger('order_status_id');
            $table->timestamps();
        });

        Schema::table('trx_orders', function($table){
            $table->foreign('order_status_id')->references('id')->on('mst_order_status')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trx_orders');
    }
}
