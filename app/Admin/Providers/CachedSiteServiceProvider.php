<?php

namespace App\Admin\Providers;

use App\Admin\Modules\Sites\CachedSite;
use App\Admin\Modules\Sites\Services\CachedSiteService;
use App\Models\AirConnect\Site;
use Illuminate\Support\ServiceProvider;

class CachedSiteServiceProvider extends ServiceProvider
{
	/**
	 * Provider defers registration of Service until it is resolved the first time
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Service created in the boot method as it needs all other service prodiders registered
	 *
	 * @return void
	 */
	public function boot()
	{
		// Bind the Service as a singleton so it is only created once
		$this->app->singleton(CachedSiteService::class, function($app) {
			return new CachedSiteService(
				$app->make('Illuminate\Auth\AuthManager'),
				$app->make('Illuminate\Cache\CacheManager'),
				$app->make('Illuminate\Session\SessionManager'));
		});

		// When the Service is first resolved, call it's init() method. This is required
		// as opposed to initializing the class in the constructor due to HTTP middleware
		// needing to be ran to get the authenticated User, which is not available on boot()
		$this->app->resolving(CachedSiteService::class, function($service, $app) {
			$service->init();
		});

		// Load helper functions
		if (file_exists($file = app_path('Admin/Modules/Sites/Services/ServiceHelpers.php')))
		{
			require $file;
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [CachedSiteService::class];
	}
}