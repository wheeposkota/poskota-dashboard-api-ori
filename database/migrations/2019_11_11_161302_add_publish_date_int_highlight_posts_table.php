<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Facades\DB;

class AddPublishDateIntHighlightPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->date('publish_date')->after('modified_by')->nullable();
            $table->boolean('int_highlight')->default(0)->after('post_mime_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            if(Schema::hasColumn('posts', 'publish_date'))
            {
                $table->dropColumn('publish_date');
            }

            if(Schema::hasColumn('posts', 'int_highlight'))
            {
                $table->dropColumn('int_highlight');
            }
        });
    }
}
