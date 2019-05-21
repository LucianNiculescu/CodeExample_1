<?php

namespace App\Admin\Modules\Locations;

use \App\Admin\Helpers\BasicDatatable;
use App\Helpers\DateTime;

class Datatable extends BasicDatatable
{
	/**
	 * DB Query to get the locations for the Datatable
	 * @return array
	 */
	public static function getLocationsQuery()
	{
		// DB Query
		$query = \DB::table( 'airconnect.locations' )
			->select(
				'locations.id as id',
				'locations.name as name',
				'locations.room_no as room',
				'locations.type as type',
				'locations.updated as updated',
				'locations.status as status'
			)
			->where('locations.status','!=', 'deleted')
			->where('locations.site_id', session('admin.site.loggedin')); // This site from the session

		// Return the builder object
		return $query->get();
	}
}