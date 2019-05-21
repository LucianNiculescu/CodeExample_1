<?php

namespace App\Admin\Modules\Gateways;

use App\Models\AirConnect\Gateway as GatewayModel;
use App\Admin\Modules\Sites\Logic as Sites;
use \App\Admin\Helpers\BasicDatatable;
use App\Models\AirConnect\GatewayAttribute as GatewayAttributeModel;
use Gate;

class SetupViewData
{
    /**
	 * Setting up the create gateway view
	 * title, description, gatewayTypes, siteLocation ...etc
     * @return array
     */
    public static function create()
    {
        $actionUrl = '/' . str_replace('/create', '', \Request::path());
        $hiddenMethod = 'POST';

        // If somebody tries to create from the all gateways
		if (strpos($actionUrl, 'networking') === false)
			abort('401', trans('error.not-authorized'));

        // Data sent to the create page
        $data = [
            'title'         => trans('admin.create-gateway-title'),
            'description'   => trans('admin.create-gateway-desc'),
            'hiddenMethod'  => $hiddenMethod,
            'actionUrl'     => $actionUrl,
			'cancelUrl'		=> '/networking/gateways',
            'types'         => GatewayModel::getGatewayTypes(),
			'includeMapJs'		=> true,
        ];

        $siteId = session('admin.site.loggedin');
        // Getting the name(type) in the $site[0] and the location in $site[1]
        $site = Sites::getSiteWithType($siteId);
        $siteName = $site[0];
        $siteLocation = $site[1];
        $data['siteName'] = $siteName;
        $data['siteId'] = $siteId;
        // Setting up lat and lng to create the map
        if (!empty($siteLocation)) {
            $latLng = explode(',', $siteLocation);
            $lat = floatval($latLng[0]);
            $lng = floatval($latLng[1]);
            $data['lat'] = $lat;
            $data['lng'] = $lng;
            return $data;
        }
        return $data;
    }

	/**
	 * Setting up the Edit gateway view
	 * title, description, gatewayTypes, siteLocation ...etc
	 * @return array
	 */
    public static function edit($id)
    {
        // Setup the form's action and url
        $actionUrl = '/' . str_replace ('/edit','',\Request::path());
        $hiddenMethod = 'PUT';

		if (strpos($actionUrl, 'networking') === false)
		{
			$systemPage = false;
			$cancelUrl = '/gateways';
		}
		else
		{
			$systemPage = true;
			$cancelUrl = '/networking/gateways';
		}

		// Finding the specific gateway
		$gateway = CRUD::getGateway($id);

		// If no Gateway is found
		if(is_null($gateway))
			abort('404', trans('error.gateway-not-found', ['id'=>$id]));

		// Should only edit a gateway which is part of the current estate
		if($systemPage and !in_array($gateway->site, session('admin.site.estate')))
			abort('401', trans('error.not-authorized'));

		//Get the gateway attributes
		$attributes = GatewayAttributeModel::where(['ids' => $id, 'status' => 'active'])->get()->pluck('value', 'name');

		// Data to be sent to the Gateway edit page
        $data =	[
            'title' 		=> trans('admin.edit-gateways-title') ,
            'description' 	=> trans('admin.edit-gateways-desc') ,
            'module' 		=> $gateway ,
            'hiddenMethod' 	=> $hiddenMethod ,
            'actionUrl' 	=> $actionUrl,
			'cancelUrl'		=> $cancelUrl,
            'types'	 		=> GatewayModel::getGatewayTypes(),
			'includeMapJs'	=> true,
			'attributes'	=> $attributes
        ];

        // If it is all Gateways , then setup the sites dropdownlist with  name(type) in the $site[0] and the location in $site[1]
        if (!$systemPage)
        {
            // All gateways will send all sites to fill the drop downlist
            $sites = Sites::fillSitesList();
            $data['sites'] = $sites;
        }
        elseif (Gate::allows('access' ,'networking.gateways.move' ))
        {
            // All gateways will send all sites to fill the drop downlist
            $sites = Sites::fillSitesList(session('admin.site.estate'));
            $data['sites'] = $sites;
        }
        else
        {
            // Getting the name(type) in the $site[0] and the location in $site[1]
            $site = Sites::getSiteWithType($gateway->site);
            $siteName     = $site[0];
            $data['siteName']  = $siteName;
            $data['siteId']    = $gateway->site;
        }

        // Getting the gateway location to setup the lat and lng
        $location = $gateway->location;

        if(!empty($location))
        {
            $latLng = explode(',' , $location);
            $lat = floatval($latLng[0]);
            $lng = floatval($latLng[1]);
            $data['lat'] = $lat;
            $data['lng'] = $lng;
        }
        return $data;
    }

	/**
	 * Serverside datatable
	 * @param $systemPage
	 * @return array
	 */
    public static function serverSideDatatable($systemPage)
    {
        $gatewaysDatatable = Datatable::getTable($systemPage);

        // Hide actions for All gateways page to avoid adding new gateways using the variable variable $hideCreate
        if(!$systemPage)
            $hideCreate = 'hideCreate';
        else
            $hideCreate = '';

        $data = [
            'title' 			=> trans('admin.all-gateways'),
            'description'		=> trans('admin.all-gateways-desc'),
            'gatewaysDatatable'	=> $gatewaysDatatable,
            $hideCreate			=> true, // Setting $hideCreate variable to hide the create button in the all gateways page
        ];

        return $data;
    }

	/**
	 * Client side Datatable
	 * @param $systemPage
	 * @return array
	 */
    public static function clientSideDatatable($systemPage)
    {
        $rows = Datatable::getGateways(true , $systemPage)->toArray(); //$clientSide = true;

		$tableId = $route = 'gateways';
        if ($systemPage)
        {
            $route = 'networking/gateways';
            $tableId = 'networking-gateways';
        }
        // Hide actions for All gateways page and when the loggedin site is not a site
        if(!$systemPage or session('admin.site.type')!= 'site')
            $hideCreate = 'hideCreate';
        else
            $hideCreate = '';

        $columns = [
        	'info'				=> '' ,
			'nasid'				=> '',
			'created-updated' 	=> '',
		];

		$detailsColumns		= [
			'info' => [
				'name'		=> '\App\Admin\Helpers\Datatables\StrongColumn',
				'mac'		=> '',
				'ip'		=> '',
				'latency'	=> '\App\Admin\Helpers\Datatables\GatewayLatencyColumn',
				'type'		=> '\App\Admin\Helpers\Datatables\GatewayTypeColumn',
			],
			'created-updated' => [
				'hidden-updated'	=> '\App\Admin\Helpers\Datatables\HiddenColumn',
				'updated'			=> '\App\Admin\Helpers\Datatables\DateColumn',
				'created'			=> '\App\Admin\Helpers\Datatables\DateColumn',

			]
		];

        $data =  [
            'title' 				=> trans('admin.networking-gateways'),
            'description'			=> trans('admin.networking-gateways-desc'),
            'columns'				=> $columns,
			'detailsColumns'	    => $detailsColumns,
            'rows' 					=> $rows,
            'tableId'				=> $tableId,
            'route'				    => $route,
            $hideCreate			    => true, // Setting $hideCreate variable to hide the create button in the all gateways page
			'customActions'			=> CRUD::$customActions,
            'showActions'           => true,
            'clickableRow'          => true,
			'gateways'        		=> true,
			'hideTitleColumns'		=> ['mac', 'ip', 'name', 'hidden-updated', 'type']
        ];

        return $data;

    }
}