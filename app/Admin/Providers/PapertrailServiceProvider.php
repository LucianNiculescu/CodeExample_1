<?php

namespace App\Admin\Providers;

use Illuminate\Support\ServiceProvider;

class PapertrailServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    	//Disable log for local
		if (app('app')->environment() == 'local') return;

		//Get the monolog and system logs
		$monolog   = app(\Illuminate\Log\Writer::class)->getMonolog();
		$syslog    = new \Monolog\Handler\SyslogHandler('laravel');
		$formatter = new \Monolog\Formatter\LineFormatter('%channel%.%level_name%: %message% %extra%');

		//Set the formatter and push the notification to Papertrail
		$syslog->setFormatter($formatter);
		$monolog->pushHandler($syslog);
    }
}
