<?php

namespace App\Admin\Modules\Reports;
use App\Models\AirConnect\Gateway as GatewayModel;
use App\Admin\Modules\Gateways\Types\Logic as Types;

class Dashboard
{
	/**
	 * Getting the Average Speed Data from the reportData
	 * @param $reportData
	 * @return int|string
	 */
	public static function getAverageSpeedData($reportData)
	{
		if($reportData->isEmpty())
			return 0;

		$upload = $download = [];

		foreach ( $reportData as $macBandwidth )
		{
			$upload[] = round( (int)$macBandwidth->up / 1000000,2 ); // upload value divided by one million (will display as millions in chart)
			$download[] = round( (int)$macBandwidth->down / 1000000,2 ); // download value divided by one million (will display as millions in chart)
		}

		// Work out upload/download speed guage colour bands
		// Work out highest value
		$greenUp = ceil( max( $upload ) ); // Green band upper limit
		$greenDown = ceil( max( $download ) ); // Gren band lower limit

		// Work out 80% of highest value
		$yellowUp =  $greenUp / 100 * 80 ; // Yellow band upper limit
		$yellowDown = $greenDown / 100 * 80 ; // Yellow band lower limit

		// Work out 60% of highest value
		$redUp = $greenUp / 100 * 60 ; // Red band upper limit
		$redDown = $greenDown / 100 * 60; // Red band lower limit

		// Return [up, down] as follows
		// [value, min, med, max]
		return [[$upload[0], $redUp, $yellowUp, $greenUp ], [$download[0], $redDown, $yellowDown, $greenDown]];

	}

	/**
	 * Getting gateway logs from the mac
	 * @param $reportData
	 * @param $period
	 * @param $route
	 * @param $fromTo
	 * @param $tableName
	 * @param $mac
	 * @return null
	 */
	public static function getGatewayLogs($reportData, $period, $route, $fromTo, $tableName, $mac)
	{
		$logs = [];

		// Getting the gateway from the Mac
		$gateway = GatewayModel::getGatewayFromMac($mac);

		// Trying to create an object from the gateway type
		$gatewayApiObject = Types::getGatewayApiObject($gateway->toArray());

		//if the gateway has an API class then it will call the getlogs function
		if(!is_null($gatewayApiObject))
		{
			$logs =  $gatewayApiObject->getLogs();
		}

		// Setting up the datatable
		$data = [
			'tableId'	=> 'gateway-logs',
			'rows' 		=> $logs,
			'columns'	=> [
				'time' => '',
				'topics' => '',
				'message' => ''
			]
		];


		$view = \View::make('admin.modules.gateways.logs', $data);

		$view = $view->renderSections();

		return json_encode($view);

	}


	/**
	 * Getting gateway control data from the mac
	 * @param $reportData
	 * @param $period
	 * @param $route
	 * @param $fromTo
	 * @param $tableName
	 * @param $mac
	 * @return null
	 */
	public static function getGatewayControlData($reportData, $period, $route, $fromTo, $tableName, $mac)
	{
		$status = trans('error.gateway-connection');

		// Getting the gateway from the Mac
		$gateway = GatewayModel::getGatewayFromMac($mac);

		// Trying to create an object from the gateway type
		$gatewayApiObject = Types::getGatewayApiObject($gateway->toArray());

		//if the gateway has an API class then it will call the getlogs function
		if(!is_null($gatewayApiObject))
		{
			$status =  $gatewayApiObject->getAAAStatus();
		}

		return $status;
	}
}