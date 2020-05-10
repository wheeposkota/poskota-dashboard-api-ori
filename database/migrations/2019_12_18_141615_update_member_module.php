<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMemberModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $types = [
            \App\Models\Member::TYPE_REGULAR,
            \App\Models\Member::TYPE_VERIFIED,
        ];

        Schema::table('members', function (Blueprint $table) use ($types) {
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('avatar')->default('nopic.png');
            $table->enum('type', $types)->default(\App\Models\Member::TYPE_REGULAR);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            //
        });
    }
}
