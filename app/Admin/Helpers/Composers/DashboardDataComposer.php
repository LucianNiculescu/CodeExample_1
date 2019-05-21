<?php

namespace App\Admin\Helpers\Composers;
use App\Models\Reports\DashboardStats as DashboardStatsModel;
use App\Models\AirConnect\Gateway as GatewayModel;
use App\Admin\Modules\Gateways\Types\Logic as GatewayLogic;

/**
 * Gets the data for the dashboard widgets from the reports.dashboardstats table
 * Class DashboardDataComposer
 * @package App\Admin\Helpers\Composers
 */
class DashboardDataComposer
{

	public function compose($view)
	{
		if (session('admin.site.type') != 'site') {
			$dashboardData = DashboardStatsModel::whereIn('site', session('admin.site.children'))
				->with('site')
				->groupBy('site')
				->get();
		} else {
			$site = session('admin.site.loggedin');
			$dashboardData = DashboardStatsModel::where('site', $site)->first();

			//Check whether we need to report the gateways are not contactable
			$gatewaysNotContactable = $this->gatewaysNotContactable($site);

			if (!empty($gatewaysNotContactable)) {
				$view->with([
					'gatewaysNotContactable' => $gatewaysNotContactable
				]);
			}
		}


		// Add the dashboard stats to the view
		$view->with([
			'dashboardData' => $dashboardData
		]);

	}

	public function gatewaysNotContactable($site) {
		//Get all active gateways for the site
		$gateways = GatewayModel::where([
			['site', $site],
			['status', 'active'],
		])->get();

		// We need to report an error condition if any of the gateways cannot be contacted
		// We will build a list of problem gateways
		$gatewaysNotContactable = [];

		foreach($gateways as $gateway) {
			// Use an encapsulation of the gateway's API
			$gatewayApiObject = GatewayLogic::getGatewayApiObject($gateway->toArray());
			//
			if(!is_null($gatewayApiObject)) {
				//If the name does not match then conclude we cannot contact the gateway
				if (! $gatewayApiObject->matchingGatewayName($gateway->name) ) {
					$gatewaysNotContactable[] = $gateway->name;
				}
			} else {
				// If we can't get an API object, then we can't contact the gateway and we need to report an error
				$gatewaysNotContactable[] = $gateway->name;
			}
		}

		return $gatewaysNotContactable;
	}
}