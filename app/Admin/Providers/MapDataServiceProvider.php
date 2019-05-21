<?php

namespace App\Admin\Providers;

use Illuminate\Support\ServiceProvider;

class MapDataServiceProvider extends ServiceProvider
{

	private static $mapDataRoutes = [
		'admin.templates.system.widgets.map',
	];

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		view()->composer(Self::$mapDataRoutes, '\App\Admin\Helpers\Composers\MapDataComposer');
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