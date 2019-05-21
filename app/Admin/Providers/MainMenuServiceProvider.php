<?php

namespace App\Admin\Providers;
use Illuminate\Support\ServiceProvider;

class MainMenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('admin.templates.system.menus.menu', '\App\Admin\Helpers\Composers\MainMenuComposer');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    
}
