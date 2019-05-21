<?php

namespace App\Admin\Providers;
use App\Models\AirConnect;
use Illuminate\Support\ServiceProvider;
use App\Admin\Helpers\Composers;

class WidgetMenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->getInactiveWidgets();

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

    public function getInactiveWidgets()
    {
        view()->composer('admin.modules.sites.dashboard', '\App\Admin\Helpers\Composers\WidgetMenuComposer');
//        view()->composer('admin.templates.system.menus.widget_menu', '\App\Admin\Helpers\Composers\WidgetMenuComposer');
    }

}
