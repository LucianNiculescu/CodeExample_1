<?php

namespace App\Admin\Modules\Blacklist;

use App\Admin\Modules\Blacklist\Requests\StoreRequest;
use App\Models\AirConnect\Site;
use App\Models\AirConnect\Blocked;

class Logic
{
	/**
	 * Creates new Blocked record(s) from a request. If user has specified to block
	 * MAC address from estate, all records are created
	 *
	 * @param StoreRequest $request
	 * @return \Illuminate\Support\Collection
	 */
	public static function storeFromRequest(StoreRequest $request)
	{
		/**
		 * Get the site ID(s) to block the MAC from
		 * @var array
		 */
		$siteIds = $request->site_or_estate === 'site'
			? [session('admin.site.loggedin')]
			: session('admin.site.children');

		// Remove if already blocked from selected sites
		Blocked::where('mac', $request->mac)
					->whereIn('site', $siteIds)
					->delete();

		// Grab the sites and create the blocked records
		$createdRecords = collect([]);
		$sites = Site::find($siteIds);

		$sites->each(function(Site $site) use ($request, &$createdRecords) {
			$blocked = $site->blocked()->create([
				'blocker' 	=> \Auth::user()->id,
				'mac'		=> $request->mac,
				'reason'	=> $request->reason
			]);

			$createdRecords->push($blocked);
		});

		return $createdRecords;
	}
}