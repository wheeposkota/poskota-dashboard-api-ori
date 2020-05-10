<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstOrderStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_order_status', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('status_name', ['Menunggu', 'Pembayaran Dikonfirmasi', 'Lunas', 'Approved Editor', 'Sesi Ditutup', 'Batal'])->unique();
            $table->timestamps();
        });

        DB::table('mst_order_status')->insert([
            [
                'status_name' => 'Menunggu',
                'created_at' => \Carbon\Carbon::now()
            ],
            [
                'status_name' => 'Pembayaran Dikonfirmasi',
                'created_at' => \Carbon\Carbon::now()
            ],
            [
                'status_name' => 'Lunas',
                'created_at' => \Carbon\Carbon::now()
            ],
            [
                'status_name' => 'Approved Editor',
                'created_at' => \Carbon\Carbon::now()
            ],
            [
                'status_name' => 'Sesi Ditutup',
                'created_at' => \Carbon\Carbon::now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_order_status');
    }
}
