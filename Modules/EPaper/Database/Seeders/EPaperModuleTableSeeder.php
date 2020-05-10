<?php

namespace Modules\EPaper\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use DB;

class EPaperModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('module')->insert([
            [
                'name' => 'EPaper',
                'slug' => 'epaper',
                'scope' => json_encode(array('menu', 'create', 'read', 'update', 'delete', 'permission')),
                'is_scanable' => '1',
                'created_at' => \Carbon\Carbon::now()
            ]
        ]);
    }
}
