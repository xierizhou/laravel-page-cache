<?php
namespace Rizhou\PageCache;

use Illuminate\Support\ServiceProvider;
use Rizhou\PageCache\Console\ClearCommand;
use Rizhou\PageCache\Middleware\PageCacheResponse;

class LaravelServiceProvider extends ServiceProvider
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

        $this->addMiddlewareAlias('page.cache',PageCacheResponse::class);
    }

    protected function addMiddlewareAlias($name,$class){
        $router = $this->app['router'];

        if(method_exists($router,'aliasMiddleware')){
            return $router->aliasMiddleware($name,$class);
        }

        return $router->middleware($name,$class);
    }

}
