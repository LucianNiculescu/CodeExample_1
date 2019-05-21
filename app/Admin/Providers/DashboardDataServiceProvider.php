<?php

namespace App\Admin\Providers;

use Illuminate\Support\ServiceProvider;

class DashboardDataServiceProvider extends ServiceProvider
{

	private static $dashboardDataRoutes = [
		'admin.modules.sites.dashboard', /*'admin.modules.reports.dashboard',*/
	];

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		view()->composer(Self::$dashboardDataRoutes, '\App\Admin\Helpers\Composers\DashboardDataComposer');
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{

	}
}