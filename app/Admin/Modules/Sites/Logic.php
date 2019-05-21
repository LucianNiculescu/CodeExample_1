<?php
/**
 * Created by PhpStorm.
 * User: sheri
 * Date: 4/28/2016
 * Time: 11:49 AM
 */

namespace App\Admin\Modules\Sites;

use App\Admin\Modules\Packages\Services\InheritanceService;
use App\Admin\Modules\Sites\EstateSingleton as Estate;
use App\Models\AirConnect\Package as PackageModel;
use App\Models\AirConnect\Site as SiteModel;
use App\Models\AirConnect\Gateway as GatewayModel;
use App\Models\AirConnect\Portal as PortalModel;
use App\Models\AirConnect\PortalAttribute as PortalAttributeModel;
use App\Models\AirConnect\SiteAttribute as SiteAttributeModel;
use App\Models\Simplifi\Adserver as AdserverModel;
use App\Models\SimplifiDB\Venue as VenueModel;
use App\Admin\Modules\Sites\Upgrade\Pms as PmsUpgrade;

class Logic
{

	/**
	 * Setting up the session variables
	 * @param $siteId
	 * @param bool $newSite
	 */
    public static function setupSession($siteId, $newSite = false)
    {
    	$cachedSite = logged_in_site()
                        ? cached_site_service()->loggedInCachedSite()
                        : cached_site_service($siteId);

		// If it is a new site don't setup the loggedin site and the sitetype
		if(!$newSite)
		{
			session( ['admin.site.loggedin' => $cachedSite->site()->id ] );
			session( ['admin.site.type' =>	$cachedSite->type() ]);
			\Cache::forget('admin.main-menu');
		}

		session( ['admin.site.path' => $cachedSite->pathForUser()->pluck('id')->toArray() ] );

		// Set the admin.site.children variable, which is the estate of the logged in site (or User's Site)
		session( ['admin.site.children' => $cachedSite->estate()->pluck('id')->toArray() ] );

		// Set the admin.site.estate variable, which is the estate of the User's Site
		session( ['admin.site.estate' => user_estate()->pluck('id')->toArray() ]);

        // Add in the Package types active on the site
		session( ['admin.site.active_package_types' => $cachedSite->packageTypes()->toArray() ] );
    }

	/**
	 * setLoggedInSession saves the logged in Site into session 'admin.site.loggedin'
	 * @param $siteId
	 */
	public static function setLoggedInSession($siteId)
	{
		session( ['admin.site.loggedin' => $siteId ] );
	}


	/**
	 * Path returns the parents of the siteId until you find an estate
	 * $includeEstate will determine to include the estate or not, default is truee
	 * estate site ID is saved in $estateId
	 * @param $startSiteId 
	 * @param $stopSiteId = null default will bring the admin.site from the session 
	 * @param $includeEstate is a boolean to include the root site id or not 
	 * @return array of Parents from bottom to top ex: if siteID is 551 result will be [15,13,3,1,0]
	 */
	public static function path($startSiteId , $stopSiteId = null, $includeEstate = true)
	{
		// starting an Estate Singlton and get the path
		$estateObj = Estate::getInstance();
		$path = $estateObj->path($startSiteId , $stopSiteId, $includeEstate);

        // saving path into session
		session( ['admin.site.path' => $path ] );
		return $path;
	}

	/**
	 * children gets all children and grand children of the siteID "included"
	 * @param $siteId
	 * @return array of site ids
	 */
	public static function children($siteId = null)
	{
		// starting an Estate Singlton and get the children
		$estateObj = Estate::getInstance();
		$children = $estateObj->children($siteId);

		// saving children into session
		session( ['admin.site.children' => $children ] );
		return $children;
	}

	/**
	 * estate it is a combination between path and children, so UP and Down including siteID and Estate ID
	 * @param $siteId
	 * @return array of site ids
	 */
	public static function estate($siteId = null)
	{
/*		if (session('admin.site.estate'))
		{
			// if estate is already in the session then use it
			return session('admin.site.estate');
		}*/

		// starting an Estate Singlton and get the Estate
		$estateObj = Estate::getInstance();
		$estate = $estateObj->estate($siteId);

		// saving estate into session
		session( ['admin.site.estate' => $estate ] );
		return $estate;
	}


	/**
	 * Add the siteType to the session
	 * @param null $siteId 	ID of the site to get the type for

	 */
	public static function siteType($siteId = null)
	{
		// starting an siteType Singlton and get the Type
		$typeObj = Estate::getInstance();
		$typeObj->addCurrentSiteTypeToSession($siteId);

	}


	/**
	 * Retrieves the estate names and put it in an array key is the siteId and value is siteName
	 * @return array
	 */
	public static function getEstateNames()
	{
		$estateIds 	= session('admin.site.estate');

		$estate =
			SiteModel::select('id', 'name')
				->whereIn('id', $estateIds)
				->get()
				->toArray();

		$estateData = [];

		foreach($estate as $site)
		{
			$estateData[$site['id']] = $site['name'];
		}

		return $estateData;
	}

	/**
	 * Getting sites to fill the datatable
	 * @param bool $clientSide
	 * @return $this|array|static[]
	 */
	public static function getSites($clientSide = false , $estatePage = false)
	{
		// If previous url was dashboard then it is the widget so only show children
		if(\Request::ajax() and strpos(\URL::previous(), 'dashboard') !== false)
			$siteIds = session('admin.site.children');
		else
			$siteIds = session('admin.site.estate');

		//TODO: Check Elequent to see if it is faster or not?
		// Create the query to run the estate datatable
		$sites = \DB::table( 'site' )
			//->orderBy('site.id')
			->leftJoin('site_attribute', function ($join) {
				$join->on('site.id', '=', 'site_attribute.ids')
					->where('site_attribute.name' , '=', 'sitetype');
			})
			->select('site.id as id','site.name as name', 'site.reference as reference','site_attribute.value as type' , 'site.status as status' , 'site.version as version' )
			->where( 'site.status','!=', 'deleted' )
			->where( 'site.id','!=', 1 );

		// If estate page then show only sites in your estate
		if($estatePage)
		{
			$sites = $sites->whereIn( 'site.id', $siteIds );
		}


		// Special code for client side datatable
		if ($clientSide)
			$sites = $sites->get();

		return $sites;
	}

	/**
	 * Counting sites that are not deleted and not site number 1
	 * @return int

	public static function countSites()
	{
		$count = \DB::table( 'site' )
			->where( 'site.status','!=', 'deleted' )
			->where( 'site.id','!=', 1 )
			->count();

		return $count;
	}
	 */
    /**
     * Get Sites and type details either all sites or a list of sites passed as an array
     * @param string $list
     * @return array|\Illuminate\Database\Eloquent\Builder|static
     */
	public static function getSitesWithType($list = 'all')
    {
        $sites = SiteModel::with(['attributes' => function ($q) {
                        $q->where('site_attribute.name', '=', 'sitetype');
                    }]);


        if($list != 'all')
            $sites = $sites->whereIn('id',$list);

		$sites = $sites->where('id', '!=', '1')
			->where( 'site.status','!=', 'deleted' );

        $sites = $sites->get()
            ->toArray();

        return $sites;
    }

    /**
     * Get a list of sites and put them in a result array with the site id as key and name with type as value
     * i.e. 3 => Airangel (Estate), 13 => Park Plaza Company (Company)
     * @param string $list
     * @return array
     */
    public static function fillSitesList($list = 'all')
    {
        $sites = self::getSitesWithType($list);

        $result = [];
        foreach($sites as $site)
        {
            $result[$site['id']][0] = $site['name'];
            $result[$site['id']][1] = $site['location'];
            $result[$site['id']][2] = $site['reference'];
            self::addTypeToName($result[$site['id']][0], $site) ;
        }

        return $result;
    }

    /**
     * Get specific site name and put the type after it like Airangel (Estate)
     * @param $siteId
     * @return array where index 0 is the site name and 1 is the location
     */
    public static function getSiteWithType($siteId)
    {
        $getSite    = self::getSitesWithType([$siteId]);

        $site       = $getSite[0];

        self::addTypeToName($name, $site) ;

        $result[0] = $site['name'];
        $result[1] = $site['location'];
        $result[2] = $site['reference'];

        return $result;
    }


    /**
     * Adds site type to name to show like Airangel (Estate)
     * @param $name
     * @param $site
     */
    public static function addTypeToName(&$name , $site)
    {
        if(!empty($site['attributes']))
            $name = $name . ' (' . trans('admin.'.$site['attributes'][0]['value']) . ')';
    }


	/**
	 * Fills a list of the id and site to be used in a dropdownlist for example
	 * @param string $list
	 * @return mixed
	 */
	public static function getSiteList($list = 'all')
	{
	    // Get the estate of the top level Site, or the User's Site
	    $siteListFromCache = $list === 'all'
                                ? cached_site_service(1)->estate()
                                : cached_site_service()->userCachedSite()->estate();

	    // Don't include deleted Sites
        $siteList = $siteListFromCache->whereIn('status', ['active', 'inactive']);

        // Return the Sites as a list
        return $siteList->pluck('name', 'id');
	}

	/**
	 * Getting Site Attributes
	 * @param $id
	 * @return mixed
	 */
	public static function getSiteAttributes($id){
		$attributes = SiteAttributeModel::where(['ids' => $id, 'status' => 'active'])->get();
		return $attributes = $attributes->pluck('value', 'name');
	}


	/**
	 * Getting the lat and long from an address
	 * @param $address
	 * @return array
	 */
	public static function getLatLong($address)
	{
		$prepAddr = str_replace(' ','+',$address);
		$geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
		$output= json_decode($geocode);
		$lat = $output->results[0]->geometry->location->lat;
		$long = $output->results[0]->geometry->location->lng;
		return [$lat, $long];
	}

	/**
	 * Get default location either from first gateway or london
	 * @param $data
	 */
	public static function getDefaultLocation(&$data)
	{
		$latLng = [0, 0];
		$location = '';
		$found 	= false;
		$siteId = isset($data['siteId']) ? $data['siteId'] : null;


		// Editing a site
		if(!is_null($siteId))
		{
			$address = self::getAddressFromSiteAttributes($siteId);

			if(!empty($address))
			{
				$found = true;
				$latLng = self::getLatLong($address);
			}

			if(!$found)
			{
				$location = self::getLocationFromGateways($siteId);
				if($location != '')
					$found = true;
			}
		}

		// For editing or new sites search locations in the parents' path
		if(!$found)
			$location = self::getLocationFromPath();

		// Create a latLng array from location if exists
		if($location != '')
			$latLng = explode(',', $location);

		if(is_array($latLng) && $latLng[0] > 0  && $latLng[0] > 0 )
        {
            $lat = floatval($latLng[0]);
            $lng = floatval($latLng[1]);

            $data['lat'] = $lat;
            $data['lng'] = $lng;
        } else {
		    $data['lat'] = config('app.location_default.lat');
		    $data['lng'] = config('app.location_default.lng');
        }
	}

	/**
	 * Getting the site address from the site attributes
	 * @param $id
	 * @return string
	 */
	public static function getAddressFromSiteAttributes($id): string
	{
		// Checking Site Attributes for address
		$siteAttributes = self::getSiteAttributes($id);
		$address = '';
		$addressAttributes = ['address1', 'address2', 'town', 'address'];

		foreach ($siteAttributes as $attributeKey => $attributeValue)
			if (in_array($attributeKey, $addressAttributes))
				$address .= $attributeValue . ' ';

		return trim($address);
	}

	/**
	 * Getting the first location from the gateways as string
	 * @param $id
	 * @return string
	 */
	public static function getLocationFromGateways($id): string
	{
		$gateways = GatewayModel::getAllGatewayBySite($id);
		// Looping into gateways and return the first location found
		foreach ($gateways as $gateway)
			if(isset($gateway->location) and $gateway->location != '' and !is_null($gateway->location))
				return $gateway->location;

		return '';
	}

	/**
	 * Getting the first location from the site path as string
	 * @return string
	 */
	public static function getLocationFromPath(): string
	{
		if(is_null(session('admin.site.path'))){
			$location = \Auth::user()->adminSite->location;
			return is_null($location) ? '' : $location;
		}

		// Looping into path session variable and get the first location found
		foreach (session('admin.site.path') as $siteId)
		{
			$site = SiteModel::find($siteId);
			if(isset($site->location) and $site->location != '' and !is_null($site->location))
				return $site->location;
		}

		return '';
	}

	/**
	 * If there is a Venue selected (we have venue_id) then we update `adserver` table with the gateway and package information.
	 * If we don't have a venue_id, we create a new record inside `adserver` table ( treat it as a new client ).
	 * Create a Venue if there is no venue_id, create a new adserver record or update the existing one (if venue is selected) and
	 * add gateway_id, package_id, venue_id and adjets into site_attributes
	 *
	 * @param $siteId
	 * @param $gatewayId
	 * @param $packageId
	 * @param string $venueId
	 */
	public static function setAdserverProperties($siteId, $gatewayId, $packageId, $venueId = '') {
		//Get gateway, package and some data like upstream, downstream, cost
		$gateway 		= GatewayModel::where('id', $gatewayId)->first();
		$package 		= PackageModel::where('id', $packageId)->first();
		$upstream 		= $package->getPackageAttributeValue('upstream', 1024).'k';
		$downstream 	= $package->getPackageAttributeValue('downstream', 1024).'k';
		$cost 			= $package->getPackageAttributeValue('cost', 1);

		//based on the venueId, create the venue or retrieve it
		if(empty($venueId)) {

			$site = SiteModel::where('id', $siteId)->first();

			$venue 			= new VenueModel;
			$venue->name 	= $site->name;
			$venue->version = 3;
			$venue->save();

			//create the Adserver record
			$adserver = new AdserverModel;

			//insert the venue into site_attributes
			$siteAttribute = new SiteAttributeModel();
			$siteAttribute::insert([
				'ids' 		=> $siteId,
				'name' 		=> 'venue_id',
				'type'		=> 'site',
				'value' 	=> $venue->id,
				'status' 	=> 'active'
			]);

		} else {
			$venue 		= VenueModel::where('id', $venueId)->first();
			$adserver	= AdserverModel::where('venue_id', $venue->id)->first();
		}

		//update or add the data into adserver table
		$adserver->venue_id 			= $venue->id;
		$adserver->type 				= strtolower($gateway->type);
		$adserver->ip 					= $gateway->ip;
		$adserver->username 			= $gateway->username;
		$adserver->password 			= $gateway->password;
		$adserver->port 				= 8728;
		$adserver->timeout 				= 5;
		$adserver->free_bandwidth 		= $upstream.'/'.$downstream;
		$adserver->premium_bandwidth 	= $upstream.'/'.$downstream;
		$adserver->increase_bandwidth 	= '500k/500k';
		$adserver->currency 			= 'GBP';
		$adserver->premium_cost 		= $cost;
		$adserver->premium_ads			= 0;
		$adserver->gateway_mac 			= $gateway->mac;
		$adserver->save();
	}


	/**
	 * Checks the permission of the site
	 * @param $permission
	 * @return bool
	 */
	public static function getSitesPermission($permission)
	{
		$result = false;

		if(\Gate::allows('access' , 'all-sites.' . $permission ))
			$result = true;
		else if(strpos( \Request::path(), 'manage') !== false and \Gate::allows('access' , 'manage.sites.' . $permission ))
			$result = true;

		return $result;
	}

	/**
	 * Checks if the URL::previous contains the needle
	 * @param $needle
	 * @return bool
	 */
	public static function checkBackUrl($needle) {
		if(strpos(\URL::previous(), $needle) > 0)
			return true;

		return false;
	}


	/**
	 * Creates an object to take the requested upgrade action
	 * At present, this does not use version types but it can be easily extended to pass those through
	 * @param $upgradeAction
	 * @param $site
	 * @param $lowVersion - what we are upgrading from. Defaults to v1
	 * @param $highVersion - what we are upgrading to. Defaults to v3
	 * @return mixed
	 */
	public static function getUpgradeObject($upgradeAction, $site, $lowVersion = 1, $highVersion = 3)
	{
		$pmsClass = '\App\Admin\Modules\Sites\Upgrade\\' .ucwords( strtolower($upgradeAction));
		return new $pmsClass($site, $lowVersion, $highVersion);
	}


	/**
	 * Upgrade to latest version (v3 atm), if not there already. Otherwise store request.
	 * @param $id - Site id
	 * @return mixed
	 */
	public static function upgradeOrUpdate($id) {
		$siteCRUD = new CRUD('Site');

		// Are we to perform an upgrade?
		if (isset($siteCRUD->requestData["version"]))
		{
			// Migrate Pms settings.
			// We could check the returned boolean and take further action if it is false,
			// because it means there was a problem, but we have no defined action yet.
			$settingsMigrated = self::getUpgradeObject('Pms', $id)->up();

			// Sent via activate or delete button
			$siteCRUD->requestData["version"] = 3;
			$siteCRUD->update($id, $siteCRUD->requestData);

			$buttons = SetupViewData::actionButtons($id, $siteCRUD->requestData["route"]);

			return [1, $buttons];
		}
		else
		{
			return $siteCRUD->saveForm($id);
		}
	}
}

