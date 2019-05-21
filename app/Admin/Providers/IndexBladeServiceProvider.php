<?php

namespace App\Admin\Providers;
use App\Models\AirConnect;
use Illuminate\Support\ServiceProvider;
use App\Admin\Helpers\Composers;

class IndexBladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Setting up the IndexBladeComposer for the admin.*.index view
        view()->composer('admin.templates.system.index', '\App\Admin\Helpers\Composers\IndexBladeComposer');

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