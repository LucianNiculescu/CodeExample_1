<?php

namespace App\Jobs;

use App\Models\AirConnect\User;
use App\Models\AirConnect\Site;
use App\Transformers\GuestTransformer;
use GuzzleHttp\Psr7\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class SendGuestDataForNodin extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

	/**
	 * The Site which contains the nodin attribute to send the data to
	 *
	 * @var Site
	 */
	protected $site;

	/**
	 * The Guest whos data is being sent
	 *
	 * @var User
	 */
	protected $guest;

	/**
	 * Create a new job instance.
	 *
	 * @param  Site  $site
	 * @param  User  $user
	 */
	public function __construct(Site $site, User $user)
	{
		$this->site  = $site;
		$this->guest = $user;
	}

	/**
	 * Execute the job
	 *
	 * @return void
	 */
	public function handle()
	{
		// Get the nodin attribute from the Site attributes
		$nodinSiteAttribute = $this->site->getRelationValue('attributes')->keyBy('name')['nodin'];

		// Only send the data if the attribute is true
		if($nodinSiteAttribute->status == 'active')
		{
			// Load the Guest's attributes
			$this->guest->load('attributes');

			// Get the data we're going to send to the endpoint
			$data = GuestTransformer::transform($this->guest)->into('nodin');

			// Set the URI to post the data to from the Nodin site attribute, or dummy enpoint
			// if we're not on production
			$uri = env('APP_ENV') !== 'production'
				? 'http://checkin.airangel.net/valhala.php'
				: $nodinSiteAttribute->value;

			// Create a client with a base URI
			$client = new \GuzzleHttp\Client(['base_uri' => $uri]);

			try {
				// Post the data
				$response = $client->request('POST', $uri, $data);

				if($response->getStatusCode() != 200)
					Log::error('The endpoint (' . $uri . ') for sending Nodin data for site ' . $this->site->id . ' returned a status code of ' . $response->getStatusCode());

			} catch (\Exception $e) {

				// Log exception
				Log::error('There was an error sending Nodin data for site ' . $this->site->id . ' - ' . $e->getMessage());
			}
		}
	}
}