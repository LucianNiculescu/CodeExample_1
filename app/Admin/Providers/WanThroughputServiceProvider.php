<?php

namespace App\Admin\Providers;
use App\Models\AirConnect;
use Illuminate\Support\ServiceProvider;
use App\Admin\Helpers\Composers;

class WanThroughputServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		view()->composer('admin.test', '\App\Admin\Helpers\Composers\WanThroughputComposer');
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
