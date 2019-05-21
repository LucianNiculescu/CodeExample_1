<?php

use Symfony\Component\DomCrawler\Crawler;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

	/**
	 * Visits the given URI with a GET request. Overrides the method of the same name
	 * under the InteractsWithPages trait to include the correct $_SERVER var which
	 * is required by \App\Helpers\Language
	 *
	 * @param  string  $uri
	 * @param  array   $parameters
	 * @param  array   $cookies
	 * @param  array   $files
	 * @param  array   $server
	 * @param  string  $content
	 * @return $this
	 */
    public function visit($uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
	{
		$uri = $this->prepareUrlForRequest($uri);

		$this->call('GET', $uri, $parameters, $cookies, $files, array_merge($server, ['HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5']));

		$this->clearInputs()->followRedirects()->assertPageLoaded($uri);

		$this->currentUri = $this->app->make('request')->fullUrl();

		$this->crawler = new Crawler($this->response->getContent(), $this->currentUri);

		return $this;
	}
}
