<?php

namespace App\Admin\Providers;

use Illuminate\Support\ServiceProvider;


class UserProfileServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->getUserInfo();

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

    public function getUserInfo()
    {
        view()->composer('admin.templates.system.user_profile', '\App\Admin\Helpers\Composers\UserProfileComposer');
    }

}
