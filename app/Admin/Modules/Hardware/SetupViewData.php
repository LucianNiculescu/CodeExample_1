<?php

namespace App\Admin\Modules\Hardware;

use App\Admin\Modules\Sites\Logic as Sites;
use \App\Admin\Helpers\BasicDatatable;
use App\Models\AirHealth\Hardware as HardwareModel;

class SetupViewData
{
    /**
	 * Setting up the create hardware view
	 * title, description, hardwareTypes, siteLocation ...etc
     * @return array
     */
    public static function create()
    {
// Setup the form's action and method
        // Calculation the actionURL from the path by Removing the /create from the current path
        $actionUrl = '/' . str_replace('/create', '', \Request::path());
        $hiddenMethod = 'POST';

        // Commented code could be used if we need a drop downlist when creating
        //$sites = Sites::fillSitesList(session('admin.site.estate'));

        // Data sent to the create page
        $data = [
            'title'         => trans('admin.create-hardware-title'),
            'description'   => trans('admin.create-hardware-desc'),
            'hiddenMethod'  => $hiddenMethod,
            'actionUrl'     => $actionUrl,
			'cancelUrl'		=> '/networking/hardware',
            'types'         => HardwareModel::getHardwareTypes(),
			'includeMapJs'	=> true
            //'sites'       => $sites,
        ];

        $siteId = session('admin.site.loggedin');
        // Getting the name(type) in the $site[0] and the location in $site[1]
        $site = Sites::getSiteWithType($siteId);

        $siteLocation 	= $site[1];
        $siteReference 	= $site[2];

        $data['siteId'] 		= $siteId;
        $data['siteReference'] 	= $siteReference;

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
	 * Setting up the Edit hardware view
	 * title, description, hardwareTypes, siteLocation ...etc
	 * @return array
	 */
    public static function edit($id)
    {
        // Setup the form's action and url
        $actionUrl = '/' . str_replace ('/edit','',\Request::path());
        $hiddenMethod = 'PUT';

        // Finding the specific hardware
        $hardware = HardwareModel::find($id);

        // If no $hardware is found
        if(is_null($hardware))
            abort('404', trans('error.hardware-not-found', ['id'=>$id]));

        // Should only edit hardware in the current estate
		if(!in_array($hardware->site, session('admin.site.estate')))
			abort('401', trans('error.not-authorized'));

        // Data to be sent to the hardware edit page
        $data =	[
            'title' 		=> trans('admin.edit-hardware-title') ,
            'description' 	=> trans('admin.edit-hardware-desc') ,
            'module' 		=> $hardware ,
            'hiddenMethod' 	=> $hiddenMethod ,
            'actionUrl' 	=> $actionUrl,
			'cancelUrl'		=> '/networking/hardware',
            'types'	 		=> HardwareModel::getHardwareTypes(),
			'includeMapJs'	=> true
        ];

        // Getting the hardware location to setup the lat and lng
        $location = $hardware->location;

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

    public static function serverSideDatatable($includeGateways = false)
    {
        $hardwareDatatable = Datatable::getTable($includeGateways);

        $data = [
            'title' 			=> trans('admin.all-hardware'),
            'description'		=> trans('admin.all-hardware-desc'),
            'hardwareDatatable'	=> $hardwareDatatable,
        ];

        return $data;
    }

    public static function clientSideDatatable($includeGateways = false)
    {
        $rows = Datatable::getHardware(true, $includeGateways); //$clientSide = true;
		$tableId = $route = 'hardware';


		$route = 'networking/hardware';
		$tableId = 'networking-hardware';

		if ($includeGateways)
			$showActions = false;
		else
        	//$showActions = BasicDatatable::showActions($route, CRUD::$customActions);
			$showActions = true;

        $columns = [
        	'type'					=> '\App\Admin\Helpers\Datatables\HardwareTypeColumn',
			'info'					=> '',
			'created-updated'		=> '',
		];

		$detailsColumns		= [
			'info' => [
				'name'		=> '\App\Admin\Helpers\Datatables\StrongColumn',
				'nasid'		=> '',
				'mac'		=> '',
				'ip'		=> '',
				'latency'	=> '',
			],
			'created-updated' => [
				'hidden-updated'	=> '\App\Admin\Helpers\Datatables\HiddenColumn',
				'updated'			=> '\App\Admin\Helpers\Datatables\DateColumn',
				'created'			=> '\App\Admin\Helpers\Datatables\DateColumn',
			]
		];

		if(session('admin.site.type') != 'site')
			$detailsColumns['info']['site' ] = '';

        $data =  [
            'title' 				=> trans('admin.networking-hardware'),
            'description'			=> trans('admin.networking-hardware-desc'),
            'columns'				=> $columns,
            'detailsColumns'		=> $detailsColumns,
            'rows' 					=> $rows,
            'route'				    => $route,
			'tableId'				=> $tableId,
            'showActions'           => $showActions,
            'customActions'         => CRUD::$customActions,
			'hardware'				=> true,
			'clickableRow'          => true,
			'hideTitleColumns'		=> ['mac', 'ip', 'name', 'hidden-updated', 'type', 'site', 'nasid']
        ];

        return $data;

    }
}