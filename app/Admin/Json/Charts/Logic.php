<?php
namespace App\Admin\Json\Charts;
use DB;
/**
 * Class Logic
 * @package App\Admin\Charts
 */
class Logic
{

	public static function generateData($type, $mac, $siteId, $startDate, $endDate, $hourly)
	{
		//dd($endDate);
		$currentDate = date("Y-m-d");

		// if start date is not passed in, go back 30 days and timestamp it
		if($startDate == null)
			$startDate = date(("Y-m-d"), strtotime("-1 month", strtotime($currentDate)));


		// if end date is not passed in use the current date as a timestamp
		if($endDate == null)
			$endDate = date(("Y-m-d"), strtotime('now', strtotime($currentDate)));

		// Create new obj for this chart type
		$chartTypeObj = '\App\Admin\Json\Charts\Type\\' .ucwords( strtolower($type));

		// If the gateway type does not exist, error
		if(!class_exists($chartTypeObj))
			Admin::errorPage('404', trans('admin.chart-type-not-found', ['chartType' => $type ]));

		// Create the new obj that will create the correct chart data
		new $chartTypeObj($type, $mac, $siteId, $startDate, $endDate, $hourly);

	}

}
