<?php

namespace App\Admin\Widgets;
use App\Models\AirConnect\Gateway as GatewayModel;
use App\Models\AirHealth\Hardware as HardwareModel;
use App\Models\AirConnect\Site as SiteModel;
use App\Models\Reports\DashboardStats as DashboardStatsModel;


class Map
{
	/**
	 * Getting map data to fill the map widget
	 * @return array
	 */
	public static function getMapData()
	{
		// Call the right function depending on the site type
		if(session('admin.site.type') == 'site')
			return self::getSiteMap(session('admin.site.loggedin'));
		else
			return self::getCompanyOrEstateMap(session('admin.site.children'));
	}

	/**
	 * Getting map details for site
	 * @param $siteId
	 * @param bool $gatewaysOnly
	 * @return array
	 */
	public static function getSiteMap($siteId, $gatewaysOnly = false)
	{
		// Get the Gateways and Hardware from the DB
		$gateways = GatewayModel::where(['site' => $siteId, 'status' => 'active'])->get()->keyBy('mac');
		$hardwares = HardwareModel::where(['site' => $siteId, 'status' => 'active'])->get()->keyBy('mac');

		$locations = [];
		$lat = $long = 0;

		// Looping in hardwares and build an array of locations
		foreach($hardwares as $hardware){
			// take the updated time -  if it is over 15mins ago set status as red otherwise it will be green
			$status = self::checkHardwareStatus($hardware->updated);
			$statusClass = $status . '-map-marker';

			//check that there is a location - if not the $locationsString will stay empty and avoid error
			if(isset($hardware->location)){

				if($hardware->location != null){
					// explodes the location field into the lat and long
					$latLong = explode(',', $hardware->location);
					$lat = $latLong[0];
					$long = $latLong[1];
				}

				// checks the gateways table to see if and hardware mac addresses are in there
				if($gateways->contains('mac', $hardware->mac) == false)
					$iconClasses = $statusClass .  ' hardware-map-marker ' ;
				else
					$iconClasses = $statusClass . ' gateway-map-marker ' ;

				// Location array contains [locationData, uiData, htmlData]

				$location[0] = [
					$lat,
					$long,
					$status,
					$hardware->name
				];

				$location[1] = [
					$iconClasses,
					$hardware->users
				];

				$latency = ($hardware->packetloss == 100)? trans('admin.n-a') : $hardware->latency . 'ms';
				$location[2] = [
					'<div class="map-gateway-name"><i class="fa fa-circle '.$status.'-status "></i>&nbsp;' . $hardware->name . '</div>',
					'<div class="map-gateway-detail">'. trans('admin.latency') .': '.$latency.'</div>',
					'<div class="map-gateway-detail">'. trans('admin.online-guests') .': '.$hardware->users.'</div>',
					'<div class="map-gateway-detail">CPU: '.$hardware->cpu.'%</div>',
					'<div class="map-gateway-detail">'. trans('admin.type') . ': '.$hardware->type.'</div>',
					'<br>'

				];

				// Add only gateways if $gatewayOnly is true
				if(!$gatewaysOnly or ($gatewaysOnly and $gateways->contains('mac', $hardware->mac) == true))
					$locations[] = $location;
			}

		}

		return $locations;
	}


	/**
	 * Getting company or estate map data
	 * @param $siteList
	 * @return array
	 */
	public static function getCompanyOrEstateMap($siteList)
	{
		// Getting the sites
		$sites = SiteModel::whereIn('id', $siteList)->get();
		// Will be the JS array

		$locations = [];
		$lat = $long = 0;
		// Adds all the hardware to the JS array
		foreach($sites as $site){
			if(isset($site->location)){
				if($site->location != null and $site->location != ''){
					// explodes the location field into the lat and long
					$latLong = explode(',', $site->location);
					$lat = $latLong[0];
					$long = $latLong[1];
				}

				// Getting site details, status and number of online users
				list($gatewaysDetails, $status, $onlineGuests) = self::getSiteDetails($site->id);

				// Creating Location array [locationData, uiData, htmlData]
				$location[0] = [
					$lat,
					$long,
					$site->id,
					$site->name
				];

				$location[1] = [
					$status.'-map-marker hexagon site-map-marker',
					$onlineGuests
				];

				$location[2] = [
					'<div class="map-site-name">' . $site->name . '</div>',
					'<div class="map-site-demo"><div class="map-demo-title">'.trans('admin.demographics').'<br>('.trans('admin.guests').')</div><div class="demographicsBody"></div></div>',
					'<div class="map-site-gateways"><div class="map-gateways-title">'.trans('admin.gateways').'</div>',
				];

				// inserting into the html details the gateway details
				foreach ($gatewaysDetails as $gatewayDetails)
					foreach ($gatewayDetails as $gatewayDetail)
						array_push($location[2], $gatewayDetail);

				// If no gateways push a div with no gateways error
				if(sizeof($gatewaysDetails) == 0)
					array_push($location[2], '<div class="text-center">'.trans('admin.no-gateways').'</div>');

				array_push($location[2], '</div>');
				$locations[] = $location;
			}
		}

		return $locations;
	}

	/**
	 * getting Site details i.e. gateways , status of the site, and number of online guests
	 * @param $siteId
	 * @return array
	 */
	public static function getSiteDetails($siteId)
	{
		$gatewaysDetails 	= [];
		$redFlag   	= '';
		$greenFlag 	= '';
		$onlineGuests = 0;

		$siteLocations = self::getSiteMap($siteId, true);

		foreach($siteLocations as $location)
		{
			$gatewaysDetails[] = $location[2];
			if($location[0][2] == 'red')
				$redFlag = true;
			else
				$greenFlag 	= true;

			// Adding the onlineguests value
			$onlineGuests += $location[1][1];
		}

		// Calculating the site color if all gateways are on then green , off then red, otherwise it is yellow
		if($redFlag === true and $greenFlag === true)
			$status = 'yellow';
		elseif($redFlag === '' and $greenFlag === true)
			$status = 'green';
		else
			$status = 'red';

		return [$gatewaysDetails, $status, $onlineGuests];
	}

	/**
	 * Check the hardware status
	 * Checks the last updated time and checks it against the time passed into $checkTime
	 * to set the status as active or inactive
	 * @param $updatedTime
	 * @return string
	 */
	public static function checkHardwareStatus($updatedTime, $checkTime = "-15 minutes")
	{
		if($updatedTime <= date('Y-m-d H:i:s', strtotime($checkTime)))
			return 'red';

		return 'green';

	}

	/**
	 * Getting the demographics data for the site that is passed via AJAX request
	 * @param $request
	 * @return array
	 */
	public static function getDemographicsData($request)
	{
		$dashboardData = DashboardStatsModel::where('site', $request->site)->first();
		return  [[number_format($dashboardData->registered_users ?? 0), 'fa-users', trans('admin.all-guests')],
			[number_format($dashboardData->reg_users_airpass ?? 0), 'fa-envelope-o', trans('admin.email')],
			[number_format($dashboardData->reg_users_facebook ?? 0), 'fa-facebook-square', trans('admin.facebook')],
			[number_format($dashboardData->reg_users_twitter ?? 0), 'fa-twitter', trans('admin.twitter')],
			[number_format($dashboardData->reg_users_linkedin ?? 0), 'fa-linkedin', trans('admin.linkedin')],
			[number_format($dashboardData->reg_users_google ?? 0), 'fa-google-plus', trans('admin.google')]
		];
	}
}