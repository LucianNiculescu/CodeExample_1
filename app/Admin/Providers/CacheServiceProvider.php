<?php

namespace App\Admin\Providers;

use App\Admin\DevTools\Cache\CacheManager;
use Illuminate\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('cache', function($app)
        {
            return new CacheManager($app);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
