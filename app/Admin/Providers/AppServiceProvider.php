<?php

namespace App\Admin\Providers;

use App\Admin\Modules\Sites\SiteObserver;
use App\Admin\Search\SearchRepository;
use App\Helpers\DateTime;
use App\Models\AirConnect\Site;
use App\Models\BaseModel;
use Illuminate\Support\ServiceProvider;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Client;
use App\Admin\Search\ElasticsearchRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
		view()->composer('admin.help-pages.button',
			'\App\Admin\Helpers\Composers\HelpButtonComposer');

        // Register the Site Observer
        Site::observe(SiteObserver::class);

        \Event::listen(['eloquent.saved: *'], function($event, $data) {
            if(is_array($data) && isset($data[0]) && $data[0] instanceof BaseModel)
            {
                if($data[0]->created == '0000-00-00 00:00:00')
                    $data->fill(['created' => DateTime::now()])->save();
            }
        });
    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
		$this->app->singleton(SearchRepository::class, function($app) {

			return new ElasticsearchRepository(
				$app->make(Client::class)
			);
		});

		$this->bindSearchClient();

        view()->composer('admin.templates.system.master',
			'\App\Admin\Helpers\Composers\GlobalInfoComposer');
    }


	private function bindSearchClient()
	{
		$this->app->bind(Client::class, function ($app) {
			return ClientBuilder::create()
				->setHosts(config('services.search.hosts'))
				->build();
		});
	}
}
