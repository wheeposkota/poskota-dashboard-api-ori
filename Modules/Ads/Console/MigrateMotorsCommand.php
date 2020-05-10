<?php

namespace Modules\Ads\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Modules\Ads\Entities\MstAdCategories as MstAdCategories_m;
use Modules\Ads\Entities\MstMotors as MstMotors_m;
use Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\Terms as Terms_m;
use Modules\Ads\Entities\AdTaxonomy as AdTaxonomy_m;

use Str;

class MigrateMotorsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'poskota:migrate-motors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Motors Database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $motor = MstAdCategories_m::where('mst_ad_cat_slug', 'motor')->firstOrFail();

        $master_motors = MstMotors_m::get();

        $bar = $this->output->createProgressBar($master_motors->count());

        foreach ($master_motors as $key => $mst_motor) 
        {
            $term = Terms_m::where('slug', Str::slug($mst_motor->mst_motor_name))->first();
            if(empty($term))
                $term = new Terms_m;

            $term->name = $mst_motor->mst_motor_name;
            $term->slug = Str::slug($mst_motor->mst_motor_name);
            $term->created_by = 1;
            $term->modified_by = 1;
            $term->save();

            $taxonomy = AdTaxonomy_m::where(['term_id' => $term->getKey(), 'category_id' => $motor->getKey(), 'taxonomy' => MstAdCategories_m::TAXONOMY_BRAND])->first();
            if(empty($taxonomy))
                $taxonomy = new AdTaxonomy_m;

            $taxonomy->term_id = $term->getKey();
            $taxonomy->category_id = $motor->getKey();
            $taxonomy->taxonomy = MstAdCategories_m::TAXONOMY_BRAND;

            $taxonomy->save();

            $bar->advance();
        }

        $this->info("\r\n Motors Table Has Been Migrated");

        return 0;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
