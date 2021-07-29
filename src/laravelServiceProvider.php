<?php
namespace Rizhou\PageCache;

use Illuminate\Support\ServiceProvider;
use Rizhou\PageCache\Console\ClearCommand;

class laravelServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->commands(ClearCommand::class);

        $this->mergeConfigFrom(
            dirname(__DIR__).'/config/page-cache.php', 'page-cache'
        );


    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__).'/config/page-cache.php' => config_path('page-cache.php'),
        ]);


    }
}
