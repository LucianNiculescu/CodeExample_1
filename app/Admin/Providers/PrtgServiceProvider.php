<?php

namespace App\Admin\Providers;
use Illuminate\Support\ServiceProvider;

class PrtgServiceProvider extends ServiceProvider
{


	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		view()->composer('admin.widgets.list.prtg','\App\Admin\Helpers\Composers\PrtgWidgetComposer');
	}

}
