<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommodityTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodity_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->timestamps();
            $table->softDeletes();
        });

        $type = ['Cabe', 'Beras', 'Minyak Goreng'];
        foreach ($type as $t)
            \App\Models\CommodityType::create(['type' => $t]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commodity_types');
    }
}
