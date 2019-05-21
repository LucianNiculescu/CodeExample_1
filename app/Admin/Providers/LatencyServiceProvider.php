<?php

namespace App\Admin\Providers;
use App\Models\AirConnect;
use Illuminate\Support\ServiceProvider;
use App\Admin\Helpers\Composers;

class LatencyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->composeLatency();

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

    public function composeLatency()
    {
        view()->composer('admin.templates.system.widgets.latency', '\App\Admin\Helpers\Composers\LatencyComposer');
    }
}
