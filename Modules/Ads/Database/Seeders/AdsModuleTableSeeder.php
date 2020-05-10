<?php

namespace Modules\Ads\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use DB;

class AdsModuleTableSeeder extends Seeder
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
                'name' => 'Ads',
                'slug' => 'ads',
                'scope' => json_encode(array('menu-master', 'create-master', 'read-master', 'update-master', 'delete-master', 'permission')),
                'is_scanable' => '1',
                'created_at' => \Carbon\Carbon::now()
            ]
        ]);
    }
}
