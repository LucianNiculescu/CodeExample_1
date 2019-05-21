<?php

namespace App\Admin\Helpers\Composers;

use App\Models\AirConnect\PrtgSensors as PrtgSensorsModel;
use App\Models\AirConnect\SiteAttribute as SiteAttributeModel;

class PrtgWidgetComposer {

	/**
	 * Returns the attributes that are needed for the api call
	 * @param $view
	 */
	public function compose($view)
	{
		//Populate the dropdown of prtg-avg
		$prtgAvgList = [
			600		=> trans('admin.ten-minutes'),
			1800	=> trans('admin.thirty-minutes'),
			3600 	=> trans('admin.one-hour'),
			43200 	=> trans('admin.twelve-hours'),
			86400 	=> trans('admin.one-day')
		];

		//Get the site attributes
		$prtgSiteAttributes = SiteAttributeModel::where([
			'ids' 		=> session('admin.site.loggedin'),
			'status'	=> 'active',
		])
			->whereIn('name', ['prtg_api_server', 'prtg_api_username', 'prtg_api_passhash'])
			->get()
			->pluck('value', 'name')
			->toArray();

		//Get all the sensors for this site
		$prtgGroups = PrtgSensorsModel::select('group', 'id')
			->where('site', session('admin.site.loggedin'))
			->groupBy('group')
			->get()
			->pluck('group', 'id')
			->toArray();

		$view->with('prtgSiteAttributes', $prtgSiteAttributes);
		$view->with('prtgAvgList', $prtgAvgList);
		$view->with('prtgGroups', $prtgGroups);
	}
}