<?php

namespace App\Admin\Modules\Sites;

use App\Admin\Modules\Sites\Logic as Sites;
use App\Models\AirConnect\Site as SiteModel;
use App\Helpers\DateTime;
use App\Helpers\Country;
use App\Models\SimplifiDB\Venue as VenueModel;
use App\Models\AirConnect\Gateway as GatewayModel;
use App\Admin\Modules\Notes\Logic as NotesLogic;
use App\Admin\Modules\Pms\Logic as Pms;

class SetupViewData
{
	/**
	 * Preparing the create view
	 * @return array
	 */
	public static function create()
	{
		if (strpos(\Request::path(), 'manage') === false)
		{
			$actionUrl = '/sites';
			$siteList = Sites::getSiteList();
		}
		else
		{
			$actionUrl = '/manage/sites';
			$siteList = Sites::getSiteList(session('admin.site.estate'));
		}

		$hiddenMethod = 'POST';

		// Data sent to the admin.sites.form page
		$data = [
			'title' 			=> trans('admin.new-site-title'),
			'description' 		=> trans('admin.new-site-desc'),
			'hiddenMethod' 		=> $hiddenMethod,
			'siteList' 	    	=> $siteList,
			'siteTypeList'    	=> SiteModel::getSiteTypes(),
			'pmsCheck'    		=> Pms::pmsCheck(),
			'timezoneList'  	=> DateTime::getTimeZones(),
			'country'       	=> Country::$list,
			'siteSupportList'	=> SiteModel::getSiteSupportTypes(),
			'actionUrl' 		=> $actionUrl,
			'site'          	=> '',
			'includeMapJs'		=> true
		];

		Sites::getDefaultLocation($data);
		return $data;
	}


	/**
	 * Preparing the Edit View
	 * @param $id
	 * @return array
	 */
	public static function edit($id)
	{
		// If the id is :, use the logged in ID
		if($id == ':')
			$id = session('admin.site.loggedin');

		// Setup the form's action and url
		$actionUrl = '/' . str_replace ('/edit','',\Request::path());
		$hiddenMethod = 'PUT';

		if (strpos(\Request::path(), 'manage') === false)
		{
			$managePage = false;
			$siteList = Sites::getSiteList();
			$cancelUrl = '/sites';
		}
		else
		{
			$managePage = true;
			$siteList = Sites::getSiteList(session('admin.site.estate'));
			$cancelUrl = '/estate';
		}

		$site = SiteModel::find($id);

		if(is_null($site))
			abort('404', trans('error.site-not-found'));

		// Checking if the user is allowed to edit this site or not
		if($managePage and !in_array($id, session('admin.site.estate')))
			abort('401', trans('error.not-authorized'));

		if($site->version != 3)
			abort('401', trans('error.site-not-upgraded'));

		// Forgetting the $id and the Site's children from the list
		$siteChildrenIds = cached_site_service($id)->children()->pluck('id')->toArray();
		$siteList->forget(array_merge([$id], $siteChildrenIds));

		// get the site attributes for the current site
		$siteAttributes = sites::getSiteAttributes($id);

		// get the prtg sensors for the current site
		$prtgSensors = \App\Admin\Widgets\Prtg::countPrtgSensors($id);

		//get active gateways for this site to populate AdJets dropdown
		$gateways = GatewayModel::getAllGatewayBySite($id)->pluck('name','id');
		// Get package ids for logged in site
		$packages = SiteModel::find($id)
			->packages()
			->where('status', 'active')
			->pluck('name', 'id');
		// Get venues for this site
		$venues = VenueModel::all()->pluck('name', 'id');

		// Data to be sent to the Role edit page
		$data =
			[
				'siteId'			=> $id,
				'title' 			=> trans('admin.edit-site-title'),
				'description' 		=> $site->name, //trans('admin.edit-site-desc'),
				'module'			=> $site,
				'siteList' 	    	=> $siteList,
				'siteTypeList'    	=> SiteModel::getSiteTypes(),
				'pmsCheck'    		=> Pms::pmsCheck(),
				'timezoneList'     	=> DateTime::getTimeZones(),
				'country'       	=> Country::$list,
				'hiddenMethod' 		=> $hiddenMethod,
				'siteSupportList'  	=> SiteModel::getSiteSupportTypes(),
				'actionUrl' 		=> $actionUrl,
				'cancelUrl' 		=> $cancelUrl,
				'siteAttributes' 	=> $siteAttributes,
				'prtgSensors'		=> $prtgSensors,
				'gateways'			=> $gateways,
				'packages'			=> $packages,
				'venues'			=> $venues,
				'includeMapJs'		=> true
			];

		// Getting the site location to setup the lat and lng
		$location = $site->location;

		if(!empty($location))
		{
			$latLng = explode(',' , $location);
			$lat = floatval($latLng[0]);
			$lng = floatval($latLng[1]);
			$data['lat'] = $lat;
			$data['lng'] = $lng;
		}
		else
			Sites::getDefaultLocation($data);

		return $data;
	}

	/**
	 * Setting up the client side data
	 * @return array
	 *
	public static function clientSide()
	{
		$indexRoute = 'dashboard';
		$tableId = $route = 'sites';
		$estateView = false;

		if( in_array(\Request::path(), ['estate', 'manage/sites', 'site-list-widget']) )
		{
			$estateView = true;
			$route = 'manage/sites';
			$tableId= 'manage-sites';
		}

		$rows = Sites::getSites(true , $estateView); //$clientSide = true and estate
		$showActions = BasicDatatable::showActions($route);

		$columns = [
			'id' => '',
			'name' => '',
			'reference' => '',
			'type' => '\App\Admin\Helpers\Datatables\TranslateColumn',
		];
		$translateColumns = ['type'];

		return [
			'title' 				=> trans('admin.my-estate'),
			'description' 			=> trans('admin.my-estate-desc'),
			'columns'				=> $columns,
			'translateColumns'		=> $translateColumns,
			'dateColumns'		    => [],
			'rows' 					=> $rows,
			'route'				    => $route,
			'indexRoute'			=> $indexRoute,
			'showActions'		    => $showActions,
			'tableId'				=> $tableId,
			'clickableRow' 			=> true
		];
	}//*/

	/**
	 * Setting up the server side data
	 * @param $estatePage
	 * @return array
	 */
	public static function serverSide($estatePage)
	{
		if($estatePage)
			$route = 'manage/sites';
		else
			$route = 'sites';

		// Datatable is routing to datatable/estate to bring a Json back and much more
		$estateDatatable = Datatable::getTable($estatePage);

		return  [
			'title' 			=> $estatePage ? trans('admin.my-estate') : trans('admin.all-sites'),
			'description' 		=> $estatePage ? trans('admin.my-estate-desc') : trans('admin.all-sites-desc'),
			'estateDatatable' 	=> $estateDatatable,
			'route'				=> $route,
		];
	}

	/**
	 * @param $siteId
	 * @return array|\Illuminate\Http\RedirectResponse
	 */
	public static function dashboard($siteId)
	{
		// if no siteId the session siteId is used
		if($siteId == null)
			$siteId = session('admin.site.loggedin');
		// else siteId in session is the updated
		else
			session('admin.site.loggedin', $siteId);

		// If it is still null then redirect to estates
		if($siteId == null or !in_array($siteId, session('admin.site.estate')))
			return \Redirect::to('/estate');

		Sites::setupSession($siteId);

		// Get the site info
		$site = SiteModel::find($siteId);

		//Get notes as notifications
		$notes = NotesLogic::getNotifications($siteId);

		return [
			'title' 			=> $site->name, // trans('admin.dashboard'),
			'description' 		=> trans('admin.dashboard'), //trans('admin.dashboard-description'),
			'siteId' 			=> $siteId,
			'url'               => url('/manage/sites/' . $siteId . '/edit'),
			'notifications'		=> $notes,
			'gateways' 			=> GatewayModel::getAllGatewayBySite($siteId),
			'includeMapJs'		=> true
		];
	}

	/**
	 * @param $siteId
	 * @param $route
	 * @return string
	 */
	public static function actionButtons($siteId, $route)
	{
		$site = SiteModel::find($siteId);
		$params = 'href="'.$route . '/' .$site->id.'" data-id="'.$site->id.'" data-name="'.$site->name.'" data-route="'.$route.'"';

		return Datatable::writeActions($site, $route, $params);
	}
}