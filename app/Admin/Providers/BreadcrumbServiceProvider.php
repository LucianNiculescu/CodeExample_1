<?php

namespace App\Admin\Providers;
use App\Models\AirConnect;
use Illuminate\Support\ServiceProvider;
use App\Admin\Helpers\Composers;

class BreadcrumbServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->composeBreadcrumbs();
        
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
    
    public function composeBreadcrumbs()
    {
        view()->composer('admin.templates.system.menus.breadcrumbs', '\App\Admin\Helpers\Composers\BreadcrumbComposer');
    }
}
