<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstAdTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_ad_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mst_ad_type_name', 150);
            $table->string('mst_ad_type_slug')->unique();
            $table->timestamps();
        });

        DB::table('mst_ad_types')->insert([
            [
                'mst_ad_type_name' => 'Kontrak',
                'mst_ad_type_slug' => 'kontrak',
                'created_at' => \Carbon\Carbon::now()
            ],
            [
                'mst_ad_type_name' => 'Non Kontrak',
                'mst_ad_type_slug' => 'non-kontrak',
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
        Schema::dropIfExists('mst_ad_types');
    }
}
