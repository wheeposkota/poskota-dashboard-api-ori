<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrxDoneDateTrxOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trx_orders', function (Blueprint $table) {
            $table->datetime('trx_done_date')->after('trx_order_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trx_orders', function (Blueprint $table) {
            if(Schema::hasColumn('trx_orders', 'trx_done_date'))
            {
                $table->dropColumn('trx_done_date');
            }
        });
    }
}
