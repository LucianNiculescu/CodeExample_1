<?php


namespace App\Admin\Json\Charts;
use Illuminate\Routing\Controller as BaseController;
use \App\Admin\Json\Charts\Logic as Charts;

class Controller extends BaseController
{

	public function chart($type, $mac = null, $siteId = null, $startDate = null, $endDate = null, $hourly = true)
	{

		if(is_null($siteId))
			$siteId = session('admin.site.loggedin');

		Charts::generateData($type, $mac, $siteId, $startDate, $endDate, $hourly);

	}

}