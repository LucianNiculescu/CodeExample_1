<?php

namespace App\Admin\Providers;
use App\Models\AirConnect;
use Illuminate\Support\ServiceProvider;
use App\Admin\Helpers\Composers;

class AdminWidgetsServiceProvider extends ServiceProvider
{
	private static $widgetRoutes = [
		'*dashboard'
	];

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->composeWidgets();

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

	public function composeWidgets()
	{
		view()->composer(Self::$widgetRoutes, '\App\Admin\Helpers\Composers\WidgetsComposer');
	}

}
