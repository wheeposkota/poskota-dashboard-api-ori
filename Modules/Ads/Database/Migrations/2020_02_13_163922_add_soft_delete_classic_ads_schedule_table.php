<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeleteClassicAdsScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classic_ads_schedule', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classic_ads_schedule', function (Blueprint $table) {
            if(Schema::hasColumn('classic_ads_schedule', 'deleted_at'))
            {
                $table->dropColumn('deleted_at');
            }
        });
    }
}
