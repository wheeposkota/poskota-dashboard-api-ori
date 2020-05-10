<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRelatedPost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_post', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('related_post_id');
            $table->timestamps();
        });

        Schema::table('related_post', function($table){
            $table->foreign('post_id')->references('id_posts')->on('posts')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('related_post_id')->references('id_posts')->on('posts')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_post');
    }
}
