<?php

namespace App\Admin\Widgets;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use \App\Admin\Widgets\Logic as Widgets;
use \App\Admin\Widgets\WanThroughPut as WanThroughPutWidget;
use \App\Admin\Widgets\Map as MapWidget;
use \App\Admin\Modules\Messages\Logic as Messages;


class Controller extends BaseController
{
	/**
	 * Saving widget order everytime a user moves a widget around
	 * @param Request $request
	 * @return mixed
	 */
	public function saveWidgets(Request $request)
	{
		if (\Gate::allows('access', 'widgets-editor' ))
			return Widgets::saveWidgetOrder($request);
		else
			return false;
	}


	/**
	 * Gets WanThroughPutChart data
	 * @param null $gatewayMac
	 * @param null $siteId
	 * @return mixed
	 */
	public function getWanThroughPutChartData($gatewayMac = null, $siteId = null){
		return WanThroughPutWidget::getWanThroughPutChartData($gatewayMac,$siteId);
	}

	/**
	 * Builds the data for the map
	 *
	 * Gets the hardware and gateways for the current site that are active. Checks the hardware and gateways table
	 * to find matching mac addresses. Any matches that are found are returned as gateways and the rest are marked as
	 * hardware. Also passes the last updated time to the checkStatus function to check if the hardware/gateway is
	 * active or inactive
	 * @return string
	 */
	public function getMapData(Request $request)
	{
		return MapWidget::getMapData($request);
	}


	/**
	 * Getting Demographic data
	 * @param Request $request
	 * @return array
	 */
	public function getDemographicsData(Request $request)
	{
		return MapWidget::getDemographicsData($request);
	}

	/**
	 * Get the latest 30 messages for current site
	 * @param null $siteId
	 * @param int $messageNo
	 * @return mixed
	 */
	public function messages($siteId = null, $messageNo = 30)
	{
		return Messages::getLatestMessagesForSite($siteId, $messageNo);

	}
}