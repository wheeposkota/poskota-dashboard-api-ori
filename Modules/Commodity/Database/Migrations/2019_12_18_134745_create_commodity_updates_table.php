<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommodityUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodity_updates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type_id')->unsigned()->default(0);
            $table->integer('member_id')->unsigned()->default(0);
            $table->string('content');
            $table->integer('price')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        $faker = \Faker\Factory::create();
        $member = \App\Models\Member::first();
        foreach (\App\Models\CommodityType::get() as $t)
            \App\Models\CommodityUpdate::create([
                'type_id' => $t->getKey(),
                'member_id' => $member->getKey(),
                'content' => $faker->realText(100, 2),
                'price' => $faker->numberBetween(10000, 500000),
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commodity_updates');
    }
}
