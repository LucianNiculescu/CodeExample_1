<?php

namespace App\Admin\Providers;
use App\Models\AirConnect;
use Illuminate\Support\ServiceProvider;
use App\Admin\Helpers\Composers;

class TemplateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->getCurrentTemplate();

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

    public function getCurrentTemplate()
    {
        view()->composer(['admin.templates.system.master', 'admin.login', 'admin.modules.passwords.forgot', 'admin.modules.passwords.change'], '\App\Admin\Helpers\Composers\TemplateComposer');
    }

}
