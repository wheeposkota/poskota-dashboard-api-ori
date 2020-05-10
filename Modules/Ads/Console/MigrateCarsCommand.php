<?php

namespace Modules\Ads\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Modules\Ads\Entities\MstAdCategories as MstAdCategories_m;
use Modules\Ads\Entities\MstCars as MstCars_m;
use Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\Terms as Terms_m;
use Modules\Ads\Entities\AdTaxonomy as AdTaxonomy_m;

use Str;

class MigrateCarsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'poskota:migrate-cars';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Cars Database';

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
        $car = MstAdCategories_m::where('mst_ad_cat_slug', 'mobil')->firstOrFail();

        $master_cars = MstCars_m::get();

        $bar = $this->output->createProgressBar($master_cars->count());

        foreach ($master_cars as $key => $mst_car) 
        {
            $term = Terms_m::where('slug', Str::slug($mst_car->mst_car_name))->first();
            if(empty($term))
                $term = new Terms_m;

            $term->name = $mst_car->mst_car_name;
            $term->slug = Str::slug($mst_car->mst_car_name);
            $term->created_by = 1;
            $term->modified_by = 1;
            $term->save();

            $taxonomy = AdTaxonomy_m::where(['term_id' => $term->getKey(), 'category_id' => $car->getKey(), 'taxonomy' => MstAdCategories_m::TAXONOMY_BRAND])->first();
            if(empty($taxonomy))
                $taxonomy = new AdTaxonomy_m;

            $taxonomy->term_id = $term->getKey();
            $taxonomy->category_id = $car->getKey();
            $taxonomy->taxonomy = MstAdCategories_m::TAXONOMY_BRAND;

            $taxonomy->save();

            $bar->advance();
        }

        $this->info("\r\n Cars Table Has Been Migrated");

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
