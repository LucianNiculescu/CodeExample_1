<?php
namespace App\Transformers;

use App\Admin\Modules\Sites\Services\InheritanceService as SiteInheritanceService;
use App\Admin\Modules\Packages\Services\InheritanceService as PackageInheritanceService;
use App\Models\AirConnect\Site;

class SiteTransformer extends BaseTransformer
{
	/**
	 * Transform a Site record to the format to store in Cache
	 *
	 * @param  Site $site
	 * @return array
	 */
	public function cacheFormat(Site $site)
	{
		// Get the Site type from Site Attributes
		$siteType = $site->getRelationValue('attributes')
						->where('name', 'sitetype')
						->first();

		// Get the Site's children
		$siteChildren = SiteInheritanceService::getChildren($site , true)
			->map(function($site) {
				return [
					'id' 	    => $site['id'],
					'type' 	    => is_null($site['type']) ? 'site' : $site['type'],
					'name' 	    => $site['name'],
                    'status'    => $site['status']
				];
			});

		return [
			'loggedin' 		=> $site->id,
			'children'		=> $siteChildren,
			'path'			=> SiteInheritanceService::getPath( $site ),
			'package_types' => PackageInheritanceService::getPackages($site)->where('status', 'active')->pluck('type')->unique(),
			'updated' 		=> $site->updated,
			'status'		=> $site->status,
			'attributes'	=> [
				'sitetype' => $siteType ? $siteType->value : 'site',
			],
		];
	}
}