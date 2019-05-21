<?php

namespace App\Admin\Modules\Hardware;

use Gate;
use \App\Admin\Helpers\BasicDatatable;
use App\Helpers\DateTime;
use App\Models\AirHealth\Hardware as HardwareModel;
use App\Models\AirConnect\Gateway as GatewayModel;

class Datatable extends BasicDatatable
{

    private static $route = 'hardware';
	/**
	 * Setting up datatable and send it back
	 * @return mixed
	 */
	public static function getTable($includeGateways = false)
	{
		$table = parent::getBasicTable(self::$route);

		// Type | Name | LocationName | Latency | Lastseen | IP Address
		$table = $table	->addColumn(
								'name',
								'updated',
								trans('admin.type'),
								trans('admin.info'),
								trans('admin.created-updated'),
								trans('admin.actions')
								 )
						->setOptions( [
								'retrieve'	=> true,
								'order' 	=> [3, "asc"],
								'aoColumns' => [
									['bVisible' => false],
									['bVisible' => false],
									null,
									['aTargets' => [ 3 ], 'aDataSort' => [ 0 ]],
									['aTargets' => [ 4 ], 'aDataSort' => [ 1 ]],
									// Actions column will only be visible if you have the right permissions
									['sWidth' => '60px'	, 'visible' =>  !$includeGateways  , 'bSortable' => false]
							]]);
		

			return $table->noScript();
	}

	
	/**
	 * Making the Datatable
	 * showColumns is a list of the titles of the columns
	 * addColumn is adding a column one by one, the return of the call back function determines how the output in this column will look like
	 * searchColumns list of searchable columns
	 * orderColumns list of ordable columns
	 * @param $query
	 * @return mixed
	 */
	public static function makeTable($query)
	{
	    $route = self::$route;

	    $allHardwareTypes 	= HardwareModel::getHardwareTypes();
	    $allGatewayTypes 	= GatewayModel::getGatewayTypes();
		$route = 'networking/' . $route;

		if(strpos(\URL::previous(), 'dashboard') === false)
			$dashboardPage = false;
		else
			$dashboardPage = true;

		return self::query( $query )
			// Type | Name | LocationName | Latency | Lastseen | IP Address
			->showColumns( 'name', 'updated', 'type', 'info', 'created-updated', 'actions')
			->addColumn( 'type', 			function( $hardware ) use ($route, $allHardwareTypes, $allGatewayTypes, $dashboardPage) {

				// Get the icon type
				if(in_array($hardware->type, array_keys($allGatewayTypes)))
					$iconFileName = 'gateway';
				else
					$iconFileName = (strtolower($hardware->type) == '') ? 'default' : strtolower($hardware->type);

				if (isset($allHardwareTypes[$hardware->type]))
					$title = $allHardwareTypes[$hardware->type];
				else if(isset($allGatewayTypes[$hardware->type]))
					$title = $allGatewayTypes[$hardware->type];
				else
					$title = '';

				if(!$dashboardPage)
					$icon = '<a href="/'. $route . '/' . $hardware->id .'">';
				else
					$icon = '';

				// If Updated is less than an hour ago
				if($hardware->updated < ( \Carbon\Carbon::now()->subHour() ))
					$icon .= '<img height="20" title="'.$title.'" width="20" src="/admin/templates/system/images/networkicons/'. $iconFileName . '_off.png ">';

				// If Updated is less than 15 mins ago
				elseif($hardware->updated < (\Carbon\Carbon::now()->subMinutes(15)))
					$icon .= '<img height="20" title="'.$title.'" width="20" src="/admin/templates/system/images/networkicons/'. $iconFileName . '_warn.png">';
				else
					$icon .= '<img height="20" title="'.$title.'" width="20" src="/admin/templates/system/images/networkicons/'. $iconFileName . '_on.png">';

				if(!$dashboardPage)
					$icon .= '</a>';
				// Return the type
				return $icon   ;
			} )

			->addColumn( 'info',	    	function( $hardware ) use ($route, $dashboardPage) {
				$return = '';

				// Ignore the link if on dashboard
				if(!$dashboardPage)
					$return = '<a href="/'. $route . '/' . $hardware->id .'">';

				$return .= '<div class="detail-column"><span class="detail-column-description name"><strong>' . $hardware->name .'</strong></span></div>';
				$return .= '<div class="detail-column"><span class="detail-column-description nasid">' . $hardware->nasid .'</span></div>';
				$return .= '<div class="detail-column"><span class="detail-column-description mac">' . $hardware->mac .'</span></div>';
				$return .= '<div class="detail-column"><span class="detail-column-description ip">' . $hardware->ip .'</span></div>';
				$return .= '<div class="detail-column"><span class="detail-column-title"><strong>'.trans('admin.latency').'</strong>: </span><span class="detail-column-description ">' ;

				// If packetloss is 100 then the latency is N/A
				if($hardware->packetloss == 100)
					$return .= trans('admin.n-a');
				else
					$return .= $hardware->latency;


				$return .= '</span></div>';

				if(session('admin.site.type') != 'site')
					$return .= '<div class="detail-column"><span class="detail-column-description site">' . $hardware->site .'</span></div>';

				// Ignore the link if on dashboard
				if(!$dashboardPage)
					$return .= '</a>';

				return $return;
			} )
			->addColumn( 'created-updated',	    	function( $hardware ) use ($route, $dashboardPage) {
				if(!$dashboardPage)
					$return = '<a href="/'. $route . '/' . $hardware->id .'">';
				else
					$return = '';

				$return .= '<span class="detail-column-description hidden-updated"><span style="display: none">' . $hardware->updated .'</span></span>';
				$return .= '<div class="detail-column"><span class="detail-column-title"><strong>'.trans('admin.updated').'</strong>: </span><span class="detail-column-description ">' . DateTime::medium($hardware->updated, true) .'</span></div>';
				$return .= '<div class="detail-column"><span class="detail-column-title"><strong>'.trans('admin.created').'</strong>: </span><span class="detail-column-description ">' . DateTime::medium($hardware->created, true) .'</span></div>';

				if(!$dashboardPage)
					$return .= '</a>';
				return $return;
			} )

			->addColumn( 'actions',			function( $hardware ) use ($route, $dashboardPage)
			{
				$actions = '';

				//checking hardware.edit permission to show the edit link or not
				if (self::canAccess($route, 'edit'))
					$actions .= '<a title="' . trans("admin.edit") . ' \'' . $hardware->name. '\'" 	class="action action_edit" href="/'. $route . '/' . $hardware->id . '/edit"><i class="fa fa-pencil action text-info"></i></a>';

				$actions .= '<a title="' . trans('admin.view') . ' '.$hardware->name.'" class="action action_view" href="/'. $route . '/' . $hardware->id.'" data-id="'.$hardware->id.'" data-name="'.$hardware->name.'" ><i class="fa fa-eye action text-info"></i></a>';

				//Checking hardware.delete permission to show the delete link or not
				if (self::canAccess($route, 'delete') and  $hardware->updated < (\Carbon\Carbon::now()->subHour()))
					$actions .= '<a title="' . trans("admin.delete") . ' \'' .$hardware->name . '\'" class="action action_delete" href="/'. $route . '/' . $hardware->id  . '"  data-id="'.$hardware->id.'" data-name="'.$hardware->name.'" ><i class="fa fa-trash-o action text-danger"></i></a>';

				return $actions;
			})
			->searchColumns	( 'hardware.mac', 'hardware.site', 'hardware.type' ,'hardware.name',  'hardware.nasid', 'hardware.updated:char:255' , 'hardware.extip','hardware.ip', 'site.name' )
			->orderColumns 	( 'name', 'updated', 'type' )
			->make();
	}


    /**
     * @param bool $clientSide
     * @return \Illuminate\Database\Query\Builder
     */
    public static function getHardware($clientSide = false, $includeGateways = false)
    {
        //$systemSites = [session('admin.site.loggedin')];
        $systemSites = session('admin.site.children');

        //TODO: Check Elequent to see if it is faster or not?
        // Create the query to run the datatable
        // Type | Name | LocationName | Latency | Lastseen | IP Address
        $hardware = \DB::table( 'airhealth.hardware' )
            //	->orderBy('mac')
            ->select(
                'hardware.id as id',
                'hardware.mac as mac',
                'site.name as site',
                'hardware.nasid as nasid',
                'hardware.type as type',
                'hardware.ip as ip',
                'hardware.name as name',
                'hardware.latency as latency' ,
                'hardware.packetloss as packetloss' ,
                'hardware.updated as created' ,
                'hardware.updated as updated' ,
                'hardware.updated as hidden-updated' ,
                'hardware.status as status')
			->leftJoin('site', function ($join) {
				$join->on('site.id', '=', 'hardware.site');
			})
            ->where( 'hardware.status','!=', 'deleted' );

        // Get only Hardware
        if (!$includeGateways)
			$hardware = $hardware->whereIn('hardware.type', array_keys(HardwareModel::getHardwareTypes()));

        // System page, show all hardware under your loggedin site's children

            $hardware = $hardware->whereIn( 'hardware.site', $systemSites );

        // Special code for client side datatable
        if ($clientSide)
            $hardware = $hardware->get();

        return $hardware;
    }


    /**
	 * Preparing the hhe datatable
     */
    public static function getHheMonitoring()
    {
         // Create the query to run the datatable
        // Type | Name | LocationName | Latency | Lastseen | IP Address
        $hhe = \DB::table( 'hhe.monitoring' )
            //	->orderBy('mac')
            ->select(
                'serial as id',
                'last_seen as status',
                'devicename',
                'model',
                'channel2',
                'channel5',
                'mesh',
                'last_seen',
                'uptime',
                'clients',
				'updated',
				'uptime as uptime1'
			)
            ->where( 'site','=', session('admin.site.loggedin') );

		$hhe = $hhe->get();

        return $hhe;
    }
}