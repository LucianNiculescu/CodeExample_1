<?php

namespace App\Admin\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

class ConfigServiceProvider extends ServiceProvider
{
	/**
	 * Automatically checks which configuration to use, i.e. local, dev, staging or live
	 * Using a custom config folders under /var/www/Config/ folder
	 * @return void
	 */
	public function register()
	{
		// Get the environment we are using NB. Should be local for local
		$env = env('APP_ENV');

		// Use default settings under /var/www/config if on live or production or if there is no $env
		if ( isset($env) )
		{
			$envConfigPath = config_path() . '/' .$env; //i.e. /var/www/config/local or /var/www/config/dev
			if (file_exists($envConfigPath))
			{
				$config = app('config'); //original config

				foreach (Finder::create()->files()->name('*.php')->in($envConfigPath) as $file)
				{
					// checking the custom config folder ( i.e. /var/www/config/local)
					// if it has any php file like the default Config one (i.e. /var/www/config/)
					$key_name = basename($file->getRealPath(), '.php');
					$old_values = $config->get($key_name) ?: [];
					$new_values = require $file->getRealPath();

					// Replace any matching values in the old config with the new ones.
					$config->set($key_name, array_replace_recursive($old_values, $new_values));
				}
			}
		}
	}
}