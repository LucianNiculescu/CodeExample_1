<?php

namespace App\Admin\Modules\Packages;

use \App\Admin\Helpers\BasicDatatable;
use \App\Models\AirConnect\Package;

class Datatable extends BasicDatatable
{
	/**
	 * Return the data for the packages datatable
	 *
	 * @param bool $clientSide
	 * @param int|bool $siteId
	 * @return mixed
	 */
	public static function getPackagesDatatable($clientSide = true, $siteId = false)
	{
		$siteId = $siteId === false ? session('admin.site.loggedin') : $siteId;

		// Create the query
		$query = Package::where('package.site', '=', $siteId)
			->where('package.status', '!=', 'delete')
			->select('package.id as id', 'package.site as site', 'package.name as name', 'package.description as description',  'package.type as type', 'package.cost as cost', 'package.created as created', 'package.status as status')
			->includePackageAttributes(['upstream', 'downstream', 'duration']);

		// For the client side we can just get
		if ($clientSide)
			$query = $query->get();

		// Return the builder object
		return $query;
	}
}