<?php

namespace App\Admin\Modules\Reports;

use App\Admin\Modules\Reports\Logic as Reports;

class Technology
{
	/**
	 * Getting the Average Gateway Latency Data from the reportData
	 * @param $reportData
	 * @return float|int
	 */
	public static function getAverageGatewayLatencyData($reportData)
	{
		if($reportData->isEmpty())
			return 0;

		return round($reportData->avg('avg_latency'),2);
	}

	/**
	 * Getting the Highest Gateway Latency Data from the reportData
	 * @param $reportData
	 * @return float|int
	 */
	public static function getHighestGatewayLatencyData($reportData)
	{
		if($reportData->isEmpty())
			return 0;

		return round($reportData->max('max_latency'),2);
	}

	/**
	 * Getting the Average Traffic Data from the reportData
	 * @param $reportData
	 * @return int|string
	 */
	public static function getAverageTrafficData($reportData)
	{

		if($reportData->isEmpty())
			return 0;

		$data 				= $reportData->sum('upload_total') + $reportData->sum('download_total') ;
		$activeConnections 	= $reportData->sum('active_connections');

		if ($activeConnections == 0)
			return 0;

		return number_format( round( $data / $activeConnections ) );
	}


	/**
	 * Getting the Operating System Usage Data from the reportData
	 * @param $reportData
	 * @return int|string
	 */
	public static function getOperatingSystemUsageData($reportData)
	{
		$appleUsers = $androidUsers = $blackberryUsers = $ipadUsers = $iphoneUsers = $ipodUsers = $linuxUsers = $nokiaUsers = $windowsUsers = $otherUsers = 0;
		foreach ($reportData as $newGuest)
		{
			$appleUsers 		+= $newGuest->plat_apple;
			$androidUsers 		+= $newGuest->plat_android;
			$blackberryUsers 	+= $newGuest->plat_blackberry;
			$ipadUsers 			+= $newGuest->plat_ipad;
			$iphoneUsers 		+= $newGuest->plat_iphone;
			$ipodUsers 			+= $newGuest->plat_ipod;
			$linuxUsers 		+= $newGuest->plat_linux;
			$nokiaUsers 		+= $newGuest->plat_nokia;
			$windowsUsers 		+= $newGuest->plat_windows;
			$otherUsers 		+= $newGuest->plat_unknown;
		}

		$total = $appleUsers + $androidUsers + $blackberryUsers + $ipadUsers + $iphoneUsers + $ipodUsers + $linuxUsers + $nokiaUsers + $windowsUsers + $otherUsers;
		if($total == 0)
			return 0;


		$applePercentage 		= ceil($appleUsers 	/ $total * 100);
		$androidPercentage 		= ceil($androidUsers 	/ $total * 100);
		$blackberryPercentage	= ceil($blackberryUsers	/ $total * 100);
		$ipadPercentage 		= ceil($ipadUsers 		/ $total * 100);
		$iphonePercentage 		= ceil($iphoneUsers 	/ $total * 100);
		$ipodPercentage 		= ceil($ipodUsers 		/ $total * 100);
		$linuxPercentage 		= ceil($linuxUsers 	/ $total * 100);
		$nokiaPercentage 		= ceil($nokiaUsers 	/ $total * 100);
		$windowsPercentage 		= ceil($windowsUsers 	/ $total * 100);

		$otherPercentage 		= 100  - $applePercentage + $androidPercentage + $blackberryPercentage + $ipadPercentage + $iphonePercentage + $ipodPercentage + $linuxPercentage + $nokiaPercentage + $windowsPercentage ;

		return json_encode([
			'Apple' 				=> 	$applePercentage 		,
			'Android' 				=> 	$androidPercentage 		,
			'Blackberry'			=> 	$blackberryPercentage	,
			'IPad' 					=> 	$ipadPercentage 		,
			'IPhone' 				=> 	$iphonePercentage 		,
			'IPod'	 				=> 	$ipodPercentage 		,
			'Linux' 				=> 	$linuxPercentage 		,
			'Nokia' 				=> 	$nokiaPercentage 		,
			'Windows' 				=> 	$windowsPercentage 		,
			trans('admin.others') 	=> 	$otherPercentage
		]);
	}

	/**
	 * Getting the Operating System Usage Data from the reportData
	 * @param $reportData
	 * @return int|string
	 */
	public static function getBrowserUsageData($reportData)
	{
		$androidUsers = $blackberryUsers = $chromeUsers = $firefoxUsers = $ie6Users = $ie7Users = $ie8Users = $ie9Users = $ie10Users = $ie11Users = $ipadUsers = $iphoneUsers = $ipodUsers = $mozillaUsers = $nokiaUsers = $operaUsers = $safariUsers = $otherUsers = 0;
		foreach ($reportData as $newGuest)
		{

			$androidUsers 		+= $newGuest->browser_android	 ;
			$blackberryUsers 	+= $newGuest->browser_blackberry;
			$chromeUsers 		+= $newGuest->browser_chrome	 ;
			$firefoxUsers 		+= $newGuest->browser_firefox	 ;
			$ie6Users 			+= $newGuest->browser_ie6		 ;
			$ie7Users 			+= $newGuest->browser_ie7		 ;
			$ie8Users 			+= $newGuest->browser_ie8		 ;
			$ie9Users 			+= $newGuest->browser_ie9		 ;
			$ie10Users 			+= $newGuest->browser_ie10		 ;
			$ie11Users 			+= $newGuest->browser_ie11		 ;
			$ipadUsers 			+= $newGuest->browser_ipad		 ;
			$iphoneUsers 		+= $newGuest->browser_iphone	 ;
			$ipodUsers 			+= $newGuest->browser_ipod		 ;
			$mozillaUsers 		+= $newGuest->browser_mozilla	 ;
			$nokiaUsers 		+= $newGuest->browser_nokia	 ;
			$operaUsers 		+= $newGuest->browser_opera	 ;
			$safariUsers 		+= $newGuest->browser_safari	 ;
			$otherUsers 		+= $newGuest->browser_other	 ;

		}

		$total = $androidUsers + $blackberryUsers+ $chromeUsers + $firefoxUsers + $ie6Users	+ $ie7Users + $ie8Users + $ie9Users + $ie10Users + $ie11Users + $ipadUsers + $iphoneUsers + $ipodUsers + $mozillaUsers + $nokiaUsers + $operaUsers + $safariUsers + $otherUsers ;

		if($total == 0)
			return 0;


		$androidPercentage 		= ceil($androidUsers 	/ $total * 100);
		$blackberryPercentage	= ceil($blackberryUsers/ $total * 100);
		$chromePercentage 		= ceil($chromeUsers 	/ $total * 100);
		$firefoxPercentage 		= ceil($firefoxUsers 	/ $total * 100);
		$ie6Percentage 			= ceil($ie6Users 		/ $total * 100);
		$ie7Percentage 			= ceil($ie7Users 		/ $total * 100);
		$ie8Percentage 			= ceil($ie8Users 		/ $total * 100);
		$ie9Percentage 			= ceil($ie9Users 		/ $total * 100);
		$ie10Percentage 		= ceil($ie10Users 		/ $total * 100);
		$ie11Percentage 		= ceil($ie11Users 		/ $total * 100);
		$ipadPercentage 		= ceil($ipadUsers 		/ $total * 100);
		$iphonePercentage 		= ceil($iphoneUsers 	/ $total * 100);
		$ipodPercentage 		= ceil($ipodUsers 		/ $total * 100);
		$mozillaPercentage 		= ceil($mozillaUsers 	/ $total * 100);
		$nokiaPercentage 		= ceil($nokiaUsers 	/ $total * 100);
		$operaPercentage 		= ceil($operaUsers 	/ $total * 100);
		$safariPercentage 		= ceil($safariUsers 	/ $total * 100);

		$otherPercentage 		= 100 - $androidPercentage + $blackberryPercentage +$chromePercentage + $firefoxPercentage + $ie6Percentage	+ $ie7Percentage +$ie8Percentage +$ie9Percentage +$ie10Percentage +$ie11Percentage +$ipadPercentage +$iphonePercentage +$ipodPercentage +$mozillaPercentage +$nokiaPercentage +$operaPercentage +$safariPercentage 		;


		return json_encode([
			'Android' 				=>	$androidPercentage 	,
			'Blackberry'			=>	$blackberryPercentage,
			'Chrome' 				=>	$chromePercentage 	,
			'Firefox' 				=>	$firefoxPercentage 	,
			'IE6' 					=>	$ie6Percentage 		,
			'IE7' 					=>	$ie7Percentage 		,
			'IE8' 					=>	$ie8Percentage 		,
			'IE9' 					=>	$ie9Percentage 		,
			'IE10' 					=>	$ie10Percentage 	,
			'IE11' 					=>	$ie11Percentage 	,
			'IPad' 					=>	$ipadPercentage 	,
			'IPhone' 				=>	$iphonePercentage 	,
			'IPod' 					=>	$ipodPercentage 	,
			'Mozilla' 				=>	$mozillaPercentage 	,
			'Nokia' 				=>	$nokiaPercentage 	,
			'Opera' 				=>	$operaPercentage 	,
			'Safari' 				=>	$safariPercentage	,
			trans('admin.others') 	=>  $otherPercentage

		]);
	}


	/**
	 * Getting the Operating System Trends Data from the reportData
	 * @param $reportData
	 * @param $period
	 * @return int|string
	 */
	public static function getOperatingSystemTrendsData($reportData, $period)
	{
		$categories = [];
		$guests = [];

		foreach($reportData as $newGuest)
		{
			$guests['Apple'][] 					= intval($newGuest->plat_apple);
			$guests['Android'][] 				= intval($newGuest->plat_android);
			$guests['Blackberry'][]  			= intval($newGuest->plat_blackberry);
			$guests['IPad'][] 					= intval($newGuest->plat_ipad);
			$guests['IPhone'][] 				= intval($newGuest->plat_iphone);
			$guests['IPod'][] 					= intval($newGuest->plat_ipod);
			$guests['Linux'][] 					= intval($newGuest->plat_linux);
			$guests['Nokia'][] 					= intval($newGuest->plat_nokia);
			$guests['Windows'][] 				= intval($newGuest->plat_windows);
			$guests[trans('admin.others')][] 	= intval($newGuest->plat_unknown);
			$categories[] =  Reports::getPeriodChartCategory($period, $newGuest);
		}

		return json_encode([$categories, $guests]);

	}


	/**
	 * Getting the browser Trends Data from the reportData
	 * @param $reportData
	 * @param $period
	 * @return int|string
	 */
	public static function getBrowserTrendsData($reportData, $period)
	{
		$categories = [];
		$guests = [];

		foreach($reportData as $newGuest)
		{
			$guests['Android'][]			=	intval($newGuest->browser_android);
			$guests['Blackberry'][]			=	intval($newGuest->browser_blackber);
			$guests['Chrome'][]				=	intval($newGuest->browser_chrome);
			$guests['Firefox'][]			=	intval($newGuest->browser_firefox);
			$guests['Ie6'][]				=	intval($newGuest->browser_ie6);
			$guests['Ie7'][]				=	intval($newGuest->browser_ie7);
			$guests['Ie8'][]				=	intval($newGuest->browser_ie8);
			$guests['Ie9'][]				=	intval($newGuest->browser_ie9);
			$guests['Ie10'][]				=	intval($newGuest->browser_ie10);
			$guests['Ie11'][]				=	intval($newGuest->browser_ie11);
			$guests['Ipad'][]				=	intval($newGuest->browser_ipad);
			$guests['Iphone'][]				=	intval($newGuest->browser_iphone);
			$guests['Ipod'][]				=	intval($newGuest->browser_ipod);
			$guests['Mozilla'][]			=	intval($newGuest->browser_mozilla);
			$guests['Nokia'][]				=	intval($newGuest->browser_nokia);
			$guests['Opera'][]				=	intval($newGuest->browser_opera);
			$guests['Safari'][]				=	intval($newGuest->browser_safari);
			$guests[trans('admin.others')][]=	intval($newGuest->browser_other);

			$categories[] =  Reports::getPeriodChartCategory($period, $newGuest);
		}

		return json_encode([$categories, $guests]);

	}

	/**
	 * Getting the getDataTransferredData from the reportData
	 * @param $reportData
	 * @param $period
	 * @return int|string
	 */
	public static function getDataTransferredData($reportData, $period)
	{
		$categories = [];
		$guests = [];

		foreach($reportData as $newGuest)
		{
			$guests[trans('admin.uploaded')][]	 = intval($newGuest->upload_total);
			$guests[trans('admin.downloaded')][] = intval($newGuest->download_total);

			$categories[] =  Reports::getPeriodChartCategory($period, $newGuest);

		}

		return json_encode([$categories, $guests]);

	}
}