<?php

use App\Models\CommodityUpdate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCommodityUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commodity_updates', function (Blueprint $table) {
            $table->string('log')->default(CommodityUpdate::COMM_STABLE);
            $table->integer('price_before')->unsigned()->default(0);
        });

        CommodityUpdate::get()->each(function($i) {
            $status = CommodityUpdate::COMM_STABLE;

            $before = CommodityUpdate::where('type_id', $i->type_id)
                ->where('id', '<', $i->id)->latest()->first();
            if ($before) {
                if ($i->price > $before->price)
                    $status = CommodityUpdate::COMM_UP;
                if ($i->price < $before->price)
                    $status = CommodityUpdate::COMM_DOWN;

                $i->log = $status;
                $i->price_before = $before->price;
                $i->save();
            }
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
            if(Schema::hasColumn('commodity_updates', 'log'))
            {
                $table->dropColumn('log');
            }

            if(Schema::hasColumn('commodity_updates', 'price_before'))
            {
                $table->dropColumn('price_before');
            }
        });
    }
}
