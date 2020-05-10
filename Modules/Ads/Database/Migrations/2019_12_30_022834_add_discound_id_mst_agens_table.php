<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDiscoundIdMstAgensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_agens', function (Blueprint $table) {
            $table->unsignedInteger('mst_discount_id')->after('mst_ptlagen_region')->nullable();
            $table->foreign('mst_discount_id')->references('id')->on('mst_discount')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_agens', function (Blueprint $table) {
            $table->dropForeign(['mst_discount_id']);
            $table->dropColumn(['mst_discount_id']);
        });
    }
}
