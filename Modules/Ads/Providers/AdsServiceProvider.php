<?php

namespace Modules\Ads\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class AdsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->commands([
            \Modules\Ads\Console\MigrateCarsCommand::class,
            \Modules\Ads\Console\MigrateMotorsCommand::class,
        ]);

        $this->app['router']->aliasMiddleware('ads.transaction', \Modules\Ads\Http\Middleware\TransactionAds::class);
        $this->app['events']->listen(\App\Events\TransactionNotification::class, \Modules\Ads\Listeners\NotifyTransactionAdsWeb::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->bind(\Modules\Ads\Repositories\MstAdCategoriesRepository::class, function($app){
            return new \Modules\Ads\Repositories\MstAdCategoriesRepository(new \Modules\Ads\Entities\MstAdCategories, $app->make(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        });

        $this->app->bind(\Modules\Ads\Repositories\RltAdsCategoriesRepository::class, function($app){
            return new \Modules\Ads\Repositories\RltAdsCategoriesRepository(new \Modules\Ads\Entities\RltAdsCategories, $app->make(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        });

        $this->app->bind(\Modules\Ads\Repositories\TrxOrderRepository::class, function($app){
            return new \Modules\Ads\Repositories\TrxOrderRepository(new \App\Models\TrxOrder, $app->make(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        });

        $this->app->bind(\Modules\Ads\Repositories\TrxAdsWebRepository::class, function($app){
            return new \Modules\Ads\Repositories\TrxAdsWebRepository(new \Modules\Ads\Entities\TrxAdsWeb, $app->make(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        });

        $this->app->bind(\Modules\Ads\Repositories\TrxAdsClassicRepository::class, function($app){
            return new \Modules\Ads\Repositories\TrxAdsClassicRepository(new \Modules\Ads\Entities\TrxAdsClassic, $app->make(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
        });
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('ads.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'ads'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/ads');

        $sourcePath = __DIR__.'/../resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/ads';
        }, \Config::get('view.paths')), [$sourcePath]), 'ads');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/ads');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'ads');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../resources/lang', 'ads');
        }
    }

    /**
     * Register an additional directory of factories.
     * 
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
