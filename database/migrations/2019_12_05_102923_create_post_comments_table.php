<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('object_id');
            $table->unsignedBigInteger('member_id');
            $table->text('comment');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('post_comments', function($table){
            $table->foreign('object_id')->references('id_posts')->on('posts')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('post_comments');
    }
}
