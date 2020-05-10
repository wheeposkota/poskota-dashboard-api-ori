<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyNotificationPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->collation('')->change();
            $table->foreign('order_id')->references('id')->on('trx_orders')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_payments', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
    }
}
