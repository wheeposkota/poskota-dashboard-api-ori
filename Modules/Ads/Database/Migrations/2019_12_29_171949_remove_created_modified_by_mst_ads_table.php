<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCreatedModifiedByMstAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_ads', function ($table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['modified_by']);
            $table->dropColumn(['created_by', 'modified_by']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_ads', function ($table) {
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('modified_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
        });
    }
}
