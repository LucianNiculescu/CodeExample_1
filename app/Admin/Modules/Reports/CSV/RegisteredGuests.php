<?php

namespace App\Admin\Modules\Reports\CSV;

use App\Models\AirConnect\User;

class RegisteredGuests extends DefaultCSV
{

    /**
     * Generating the array with all the information used in the file
     * @return array //just add the array inside the protected member $this->reportData
     */
    protected function getReportData() {

    	// Get the Guests for this estate
		$guests = User::select(['id', 'site', 'user AS guest', 'name', 'mac', 'created'])
			->whereIn('site', $this->childrenIds)
			->whereBetween('created', $this->fromTo)
			->with('attributes')
			->groupBy('id', 'site')
			->orderBy('created')
			->get()
		;

		// Set the headers var for later use
		$headers = [];

		// Transform the guests with attributes to guests using the attribute name => value
		$guests->transform(function($guests) use (&$headers) {
			$guests->attrs = $guests->attributes()->get(); // Set the attributes as attrs so we do not conflict with Laravel
			$guests->attrs = $guests->attrs->pluck('value', 'name')->toArray(); // Just the name => value

			$combined = array_merge($guests->toArray(), $guests->attrs); // Merge the arrays

			// Unset what we do not need
			unset($combined['attrs']);
			unset($combined['attributes']);
			unset($combined['access_token']);

			// Get the headers
			$headers = array_merge($headers, array_keys($combined));

			// TODO: Change the provider
			//if( isset($combined['provider']) && $combined['provider'] == 'Airpass')
			//	$combined['provider'] = 'Email';

			// Return the flattened
			return $combined;
		});

		// Make sure the headers are unique
		$headers = array_flip(array_unique($headers));
		$headers = array_fill_keys(array_keys($headers), null);

		// Add the headers into the guest
		$guests->transform(function($guest) use ($headers) {
			return array_merge($headers, $guest);
		});

    	return $guests->toArray();
    }
}