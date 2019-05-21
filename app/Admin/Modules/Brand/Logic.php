<?php
/**
 * Created by PhpStorm.
 * User: sheri
 * Date: 11/1/2016
 * Time: 5:03 PM
 */

namespace App\Admin\Modules\Brand;

use App\Models\AirConnect\Portal as PortalModel;
use App\Models\AirConnect\Content as ContentModel;

class Logic
{
	/**
	 * Reading Portal and getting all portal to the loggedin site
	 * @param bool $onlyIds used in the edit mode to retrieve all IDs that user is allowed to edit
	 * @return mixed
	 */
	public static function getPortalsWithLanguage()
	{
		$site = session('admin.site.loggedin');

		$portals = PortalModel::with(['attributes' => function($q){
			$q->where('name', 'language');
		}])
			->select('id', 'name')
			->orderBy('name')
			->where('site', $site)
			->where('status', '!=', 'deleted')
			->get()
			->keyBy('id')
			->toArray();

		return $portals;
	}

	/**
	 * Deletes the current content sent in the request object
	 * @return int
	 */
	public static function delete()
	{
		$requestData = \Request::all();

		$content = ContentModel::find($requestData['id']);

		if(!is_null($content))
			$content->delete();

		return 1;
	}
}