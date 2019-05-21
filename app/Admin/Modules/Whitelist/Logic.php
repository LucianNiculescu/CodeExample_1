<?php

namespace App\Admin\Modules\Whitelist;

use App\Admin\Modules\Whitelist\Requests\StoreRequest;
use App\Models\AirConnect\Site;
use App\Models\AirConnect\Whitelist;

class Logic
{
	/**
	 * Creates new Whitelist record(s) from a request. If user has specified to whitelist
	 * MAC address on estate, all records are created
	 *
	 * @param StoreRequest $request
	 * @return \Illuminate\Support\Collection
	 */
	public static function storeFromRequest(StoreRequest $request)
	{
		/**
		 * Get the site ID(s) to whitelist the MAC on
		 * @var array
		 */
		$siteIds = $request->site_or_estate === 'site'
			? [session('admin.site.loggedin')]
			: session('admin.site.children');

		// Remove if already whitelisted from selected sites
		Whitelist::where('mac', $request->mac)
					->whereIn('site', $siteIds)
					->delete();

		// Grab the sites and create the blocked records
		$createdRecords = collect([]);
		$sites = Site::find($siteIds);

		$sites->each(function(Site $site) use ($request, &$createdRecords) {
			$blocked = $site->whitelist()->create([
				'performedby' 	=> \Auth::user()->username,
				'mac'			=> $request->mac,
				'description'	=> $request->description
			]);

			$createdRecords->push($blocked);
		});

		return $createdRecords;
	}
}