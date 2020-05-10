<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyCommodityUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commodity_updates', function (Blueprint $table) {
            $table->bigInteger('member_id')->unsigned()->default(0)->change();
            $table->foreign('type_id')->references('id')->on('commodity_types')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commodity_updates', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropForeign(['member_id']);
        });
    }
}
