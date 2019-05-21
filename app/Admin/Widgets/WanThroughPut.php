<?php

namespace App\Admin\Widgets;

use App\Models\AirConnect\Gateway as GatewayModel;
use App\Admin\Modules\Gateways\Types\Logic as Types;

class WanThroughPut
{
	/**
	 * Get the gateway data by calling the API for wanthroughput
	 **/
	static function getLiveGatewayData($gatewayMac, $siteId){

		$dataBuilder = '{}';
		try{
			// get gateway data for the current site
			$gateway = GatewayModel::where(['mac' => $gatewayMac, 'site' => $siteId, 'status' => 'active'])->first();
			$gatewayApiObject = Types::getGatewayApiObject($gateway->toArray());

			if(!is_null($gatewayApiObject))
				$dataBuilder = $gatewayApiObject->getWanThroughputData();

		} catch(\Exception $e) {
			$dataBuilder = '{}';
		}

		// json decode the results of the wanthrough data
		return json_decode($dataBuilder, true);
	}


	/**
	 * Getting data from the gateway and sending the highcharts series back
	 * @param $gatewayMac
	 * @param $siteId
	 * @return string
	 */
	static function getWanThroughPutChartData($gatewayMac, $siteId)
	{

		$data = self::getLiveGatewayData($gatewayMac, $siteId);
		$colors = ['#ffaf93',
			'#ED4423',
			'#fff562',
			'#ffaef3',
			'#3c8f39',
			'#001122',
			'#7994F2',
			'#00b7ff',
			'#0eff70',
			'#ffaf0a',
			'#f1fffd'];

		if(isset($data['status']))
		{
			$seriesData = $data['status'];
		}
		else
		{
			$seriesData = [];

			if(is_array($data))
			{
				foreach ($data as $wan)
				{
					$seriesData[] = [
						'name' => 'Wan ' . $wan['wan'] . ' ' . trans('admin.download'),
						'data' => $wan['rx'],
						'color'=> array_pop ( $colors )
					];

					$seriesData[] = [
						'name' => 'Wan ' . $wan['wan'] . ' ' . trans('admin.upload'),
						'data' => $wan['tx'],
						'color'=> array_pop ( $colors )
					];
				}
			}
		}
		return json_encode($seriesData);
	}
}