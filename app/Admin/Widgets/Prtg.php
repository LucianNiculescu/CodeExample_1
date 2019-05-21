<?php

namespace App\Admin\Widgets;

use App\Helpers\UrlHelper;
use App\Models\AirConnect\PrtgSensors as PrtgSensorsModel;
use App\Models\AirConnect\SiteAttribute as SiteAttributeModel;

class Prtg
{


	/**
	 * Calls the api for the given PRTG server
	 * Inserts the sensors into prtg_sensors table
	 *
	 * @param $siteId
	 * @param $url
	 * @param $params
	 * @return bool|string
	 */
	public static function setUpPrtgServer($siteId, $url, $params) {

		//First, delete all the sensors for this site
		PrtgSensorsModel::where('site', $siteId)->delete();
		$data = self::callPrtgServer($url, $params);

		//If we have the data from PRTG server, insert it into DB and return true
		if(!empty($data) && !empty($url))
			return self::insertPrtgSensorsAndSiteAttributes($siteId, $data, $params, $url);

		//As default return false
		return false;
	}

	/**
	 * Calls the api for the given PRTG server and returns the data as json or an empty string
	 * @param $url
	 * @param $params
	 * @return string|mixed
	 */
	public static function callPrtgServer($url, $params) {
		$data = '';
		//If we have the URL and the params that we're sending to PRTG
		if(!empty($url) && !empty($params)) {

			//Try to get the sensors from PRTG server
			try {
			$prtgUrl = $url.'/api/table.json';
			$data= UrlHelper::callClient($prtgUrl, 'GET', '', $params, [], false, true);
			} catch (\Exception $e) {
//				In case of error, abort with 500 error, with the default error message
				\Log::info('Calling PRTG server: '.$prtgUrl.' failed with the following parameters: '.json_encode($params));
				if(\Request::ajax())
					return abort('500', trans('admin.whoops-something-wrong'));
				else
					return false;
			}
		}

		return $data;
	}

	/**
	 * Inserts the sensors into prtg_sensors table and returns true/false
	 * @param $siteId
	 * @param $data
	 * @param $params
	 * @param $url
	 * @return string|mixed
	 */
	public static function insertPrtgSensorsAndSiteAttributes($siteId, $data, $params, $url) {

		$data = json_decode($data);
		$sensors = $data->sensor;
		$insert = [];

		//Check if there are sensors to be saved into db and save the site attributes
		if(!empty($sensors) && is_array($sensors)) {

			// Create the site attributes
			$siteAttributesBulkInsert = [
				[
					'ids' 		=> $siteId,
					'name'		=> 'prtg_api_server',
					'type'		=> 'site',
					'value'		=> $url,
					'status'	=> 'active',
					'created'	=> \Carbon\Carbon::now()
				],
				[
					'ids' 		=> $siteId,
					'name'		=> 'prtg_api_username',
					'type'		=> 'site',
					'value'		=> $params['username'],
					'status'	=> 'active',
					'created'	=> \Carbon\Carbon::now()
				],
				[
					'ids' 		=> $siteId,
					'name'		=> 'prtg_api_passhash',
					'type'		=> 'site',
					'value'		=> $params['passhash'],
					'status'	=> 'active',
					'created'	=> \Carbon\Carbon::now()
				]
			];

			//Delete the old values
			SiteAttributeModel::where([
				'ids' 		=> $siteId,
				'status'	=> 'active',
			])->whereIn('name', ['prtg_api_server', 'prtg_api_username', 'prtg_api_passhash'])
				->delete();

			//Insert the new attributes
			SiteAttributeModel::insert($siteAttributesBulkInsert);

			foreach($sensors as $sensor) {
				//Skip the root sensor or sensors that don't have historical data [the ones that are defining a group (basically our site/company/estate)]
				if($sensor->objid == 0 || empty($sensor->device) || empty($sensor->sensor))
					continue;
				//Create the array that will be inserted into prtg_sensors table
				$insert[] = [
					'site'			=> $siteId,
					'name'			=> $sensor->device,
					'type'			=> $sensor->sensor,
					'group'			=> $sensor->group,
					'status'		=> $sensor->status,
					'status_raw'	=> $sensor->status_raw,
					'sensor_id'		=> $sensor->objid,
					'parent_id'		=> $sensor->parentid,
					'created'		=> \Carbon\Carbon::now()
				];
			}
		}

		//Insert the PRTG Sensors into table and return true
		if(!empty($insert)) {
			//Add the new sensors
			PrtgSensorsModel::insert($insert);
			//Return true
			return count($insert);
		}

		//As default, return false
		return false;
	}

	/**
	 * Counts the prtg_sensors from this site
	 * Mainly used for the input of the site
	 *
	 * @param $siteId
	 * @return string
	 */
	public static function countPrtgSensors($siteId) {
		// get the prtg sensors for the current site
		$prtgSensors = PrtgSensorsModel::where('site', $siteId)->get()->toArray();

		return count($prtgSensors);
	}

	/**
	 * Get all the PRTG names based on a given sensor_id
	 * @param null $id
	 * @return string|array
	 */
	public static function getPrtgNames($id = null) {
		if(empty($id))
			return '';

		//Get the sensor
		$prtgSensor = PrtgSensorsModel::find($id);

		//Get all the names based on that sensor's group
		$prtgNames = PrtgSensorsModel::select('name', 'id')
			->where('site', session('admin.site.loggedin'))
			->where('group', $prtgSensor->group)
			->groupBy('name')
			->get()
			->toArray();

		//Return a json_encode only if the call is Ajax
		if(\Request::ajax())
			return json_encode($prtgNames);

		//Return the array of PRTG Names
		return $prtgNames;

	}

	/**
	 * Gets PRTG Types based on a given sensor_id
	 *
	 * @param null $id
	 * @return string|array
	 */
	public static function getPrtgTypes($id = null) {
		if(empty($id))
			return '';

		//Get the PRTG Sensor
		$prtgSensor = PrtgSensorsModel::find($id);

		//Get all PRTG Types based on the name of the sensor
		$prtgTypes = PrtgSensorsModel::select('type', 'id')
			->where('site', session('admin.site.loggedin'))
			->where('name', $prtgSensor->name)
			->groupBy('type')
			->get()
			->toArray();

		//Return a json_encode only if the call is Ajax
		if(\Request::ajax())
			return json_encode($prtgTypes);

		//Return the array of PRTG Types
		return $prtgTypes;

	}

	/**
	 * Returns the data from the PRTG Server and prepares it for the drawing of the highchart
	 * @param null $id
	 * @return array|string
	 */
	public static function getPrtgData($id = null) {

		$request = \Request::all();
		if(empty($id) || empty($request))
			return '';

		//Get sensor from the model and use it's sensor_id
		$prtgSensor = PrtgSensorsModel::find($id);
		//Get the site attributes
		$prtgSiteAttributes = SiteAttributeModel::where([
			'ids' 		=> session('admin.site.loggedin'),
			'status'	=> 'active',
		])
			->whereIn('name', ['prtg_api_server', 'prtg_api_username', 'prtg_api_passhash'])
			->get()
			->pluck('value', 'name')
			->toArray();

		//Return an empty string if there are no site attributes set
		if(empty($prtgSiteAttributes))
			return '';

		//Set the date
		$fromTo = \App\Admin\Modules\Reports\Logic::setupPeriod($request['period'], $request['from'], $request['to'], false);
		//Set the URL of the PRTG server
		$url = $prtgSiteAttributes['prtg_api_server'].'/api/historicdata.json';

		$chartData = self::getPrtgDataBySensor($prtgSensor, $request['average'], $fromTo[0].'-00-00-00', $fromTo[1].'-00-00-00', $prtgSiteAttributes['prtg_api_username'], $prtgSiteAttributes['prtg_api_passhash'], $url);

		//Get previous cookie
		$prevCookie = \Request::cookie('prtgWidget_'.session('admin.site.loggedin').'_'.session('admin.user.id'));
		$cookie = [
			'site' 			=> session('admin.site.loggedin'),
			'user'			=> session('admin.user.id')
		];
		if(!empty($prevCookie)) {
			//Insert the id of the sensor with the other sensor_ids
			array_push($prevCookie['sensor_ids'], [
				'id' 			=> $id,
				'avg'			=> $request['average'],
				'sdate'			=> $fromTo[0].'-00-00-00',
				'edate'			=> $fromTo[1].'-00-00-00',
				'uniqueId'		=> $chartData['uniqueId']
			]);
			$cookie['sensor_ids'] = array_unique($prevCookie['sensor_ids'], SORT_REGULAR);
		} else {
			//Set the cookie
			$cookie['sensor_ids'][] = [
				'id' 			=> $id,
				'avg'			=> $request['average'],
				'sdate'			=> $fromTo[0].'-00-00-00',
				'edate'			=> $fromTo[1].'-00-00-00',
				'uniqueId'		=> $chartData['uniqueId']
			];
		}

		//Set the cookie for 5 years (forever)
		\Cookie::queue('prtgWidget_'.session('admin.site.loggedin').'_'.session('admin.user.id'), $cookie, 2628000, null, null, false, false);

		//Return a json_encode only if the call is Ajax
		if(\Request::ajax())
			return json_encode($chartData);

		//Return the array of PRTG Chart data
		return $chartData;
	}

	/**
	 * Gets the data from PRTG based on the sensors that are stored into cookies
	 *
	 * @return array|string
	 */
	public static function getCookieSensorsData() {

		$cookies 	= \Request::cookie('prtgWidget_'.session('admin.site.loggedin').'_'.session('admin.user.id'));
		$chartData 	= [];
		//If we have cookies set
		if(!empty($cookies)) {
			//Get the site attributes
			$prtgSiteAttributes = SiteAttributeModel::where([
				'ids' 		=> $cookies['site'],
				'status'	=> 'active',
			])
				->whereIn('name', ['prtg_api_server', 'prtg_api_username', 'prtg_api_passhash'])
				->get()
				->pluck('value', 'name')
				->toArray();

			//Set the url for the PRTG server
			$url = $prtgSiteAttributes['prtg_api_server'].'/api/historicdata.json';

			//If we have sensors in the cookie
			if(!empty($cookies['sensor_ids'])) {
				//Call the PRTG server for each sensor set into cookie
				foreach($cookies['sensor_ids'] as $sensor) {
					//Get sensor from the model and use it's sensor_id
					$prtgSensor = PrtgSensorsModel::find($sensor['id']);
					//Add the multidimensional array with the data that we retrieve from the PRTG server
					$chartData[] = self::getPrtgDataBySensor($prtgSensor, $sensor['avg'], $sensor['sdate'], $sensor['edate'], $prtgSiteAttributes['prtg_api_username'], $prtgSiteAttributes['prtg_api_passhash'], $url, $sensor['uniqueId']);
				}
			}
		}

		//Return a json_encode only if the call is Ajax
		if(\Request::ajax())
			return json_encode($chartData);

		return $chartData;
	}

	/**
	 * Gets the data from PRTG based on given params
	 *
	 * @param $sensor
	 * @param $avg
	 * @param $startDate
	 * @param $endDate
	 * @param $username
	 * @param $passhash
	 * @param $url
	 * @return array|mixed
	 */
	public static function getPrtgDataBySensor($sensor, $avg, $startDate, $endDate, $username, $passhash, $url, $uniqueId = null) {
		//Set the params that will be send to PRTG
		$params = [
			'id' 			=> $sensor->sensor_id,
			'avg'			=> $avg,
			'sdate'			=> $startDate,
			'edate'			=> $endDate,
			'usecaption'	=> '1',
			'username'		=> $username,
			'passhash'		=> $passhash
		];

		$chartData = [

		];
		//Call the PRTG api and get the historic data for the selected sensor
		try {
			$data = UrlHelper::callClient($url, 'GET', '', $params, [], false, true);

			if(!empty($data)) {
				$data = json_decode($data);
				//Manipulate the decoded data so it can be showed into highcharts
				foreach($data->histdata as $hist) {
					//Strip out all the special characters if there is no previous value
					$chartData['uniqueId'] = $uniqueId ?? trim(preg_replace('/ +/', '_', preg_replace('/[^A-Za-z0-9 ]/', '_', urldecode(html_entity_decode(strip_tags(substr($sensor->type, 0 , 5).'_'.substr($sensor->name, 0 , 5).'_'.mt_rand(100000, 999999)))))));;
					//Remove the last 00 from seconds
					for($i=0; $i < 10; $i++) {
						$hist->datetime = str_replace(':'.$i.'0:00', ':'.$i.'0', $hist->datetime);
					}
					//Set the X line
					$chartData[0][] = $hist->datetime;
					//Unset the datetime and coverage (this is always 100% for all types of sensors)
					unset($hist->datetime, $hist->coverage);
					//Use the object's properties as keys for our array
					$keys = array_keys(get_object_vars($hist));
					//Foreach key that is mapped, set it's value
					foreach($keys as $key)
						$chartData[1][$key][] = $hist->$key;
				}
			}

		} catch (\Exception $e) {
			//In case of error, abort with 500 error, with the default error message
			\Log::info('Calling PRTG server: '.$url.' failed with the following parameters when retrieving historic data for the sensor: '.json_encode($params));

			if(\Request::ajax())
				return abort('500', trans('admin.whoops-something-wrong'));
			else
				return false;
		}

		return $chartData;
	}

	/**
	 * Deletes a PRTG sensor from the cookies
	 * @param $id
	 * @return bool|mixed
	 */
	public static function deletePrtgSensorFromCookies($id) {

		if(empty($id))
			return abort('500', trans('admin.whoops-something-wrong'));

		//Remove the '_tab_tab_id' that the field has
		$uniqueId 	= str_replace('_tab_tab_id', '', $id);
		$cookies 	= \Request::cookie('prtgWidget_'.session('admin.site.loggedin').'_'.session('admin.user.id'));

		if(!empty($cookies['sensor_ids'])) {
			foreach($cookies['sensor_ids'] as $i => $sensor)
				if($sensor['uniqueId'] == $uniqueId)
					unset($cookies['sensor_ids'][$i]);

			if(count($cookies['sensor_ids']) > 0)
				//Set the cookie for 5 years (forever)
				\Cookie::queue('prtgWidget_'.session('admin.site.loggedin').'_'.session('admin.user.id'), $cookies, 2628000, null, null, false, false);
			else
				//Expire the cookie
				\Cookie::queue('prtgWidget_'.session('admin.site.loggedin').'_'.session('admin.user.id'), $cookies, 1, null, null, false, false);

			//Return a json_encode only if the call is Ajax
			if(\Request::ajax())
				return json_encode(trans('admin.success'));

			return true;
		}

		return abort('500', trans('admin.whoops-something-wrong'));
	}

	/**
	 * Deletes PRTG site attributes and the sensors that are linked to the given site
	 * @param $siteId
	 * @return 	bool
	 */
	public static function deletePrtg($siteId) {
		if(!empty($siteId)) {
			//Delete sensors from prtg_sensors linked with this site
			PrtgSensorsModel::where('site', $siteId)->delete();

			//Expire the cookie
			if( !empty( \Request::cookie('prtgWidget_'.session('admin.site.loggedin').'_'.session('admin.user.id')) ) ) {
				$cookies = \Request::cookie('prtgWidget_' . session('admin.site.loggedin') . '_' . session('admin.user.id'));
				\Cookie::queue('prtgWidget_' . session('admin.site.loggedin') . '_' . session('admin.user.id'), $cookies, 1, null, null, false, false);
			}

			//Delete site attributes from this site
			SiteAttributeModel::where([
				'ids' 		=> $siteId,
				'status'	=> 'active',
			])->whereIn('name', ['prtg_api_server', 'prtg_api_username', 'prtg_api_passhash'])
				->delete();

			//Return a json_encode only if the call is Ajax
			if(\Request::ajax())
				return json_encode(trans('admin.success'));

			return true;
		}

		return false;
	}
}