<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEpaperPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('epaper_package', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('package_name');
            $table->decimal('package_price', 13,0);
            $table->decimal('package_period', 4,0);
            $table->text('package_description');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('modified_by');
            $table->timestamps();
        });

        Schema::table('epaper_package', function($table){
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('epaper_package');
    }
}
