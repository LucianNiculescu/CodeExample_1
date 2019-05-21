<?php

namespace App\Admin\Modules\Packages\Services;

use App\Models\AirConnect\Site;
use Illuminate\Support\Collection;

/**
 * Class responsible for the Packages on a Site via inheritance
 */
class InheritanceService
{
	/**
	 * Get's the Site's packages, or recursively gets the parent's packages
	 *
	 * @param  Site $site
	 * @return Collection
	 */
	public static function getPackages(Site $site) : Collection
	{
		// Return packages if the Site has them
		if(!$site->packages->isEmpty())
			return $site->packages;

		// Get the Site's parent
		$parent = $site->parent()->first();

		// Recursively return the packages
		if($parent !== null)
			return self::getPackages($parent);

		return collect([]);
	}
}