<?php
/**
 * Created by PhpStorm.
 * User: sheri
 * Date: 5/31/2016
 * Time: 01:41 PM
 */

namespace App\Admin\Modules\Gateways;

use Gate;
use \App\Admin\Helpers\BasicDatatable;
use App\Helpers\DateTime;

class Datatable extends BasicDatatable
{

    private static $route = 'gateways';
	/**
	 * Setting up datatable and send it back
	 * @return mixed
	 */
	public static function getTable($systemPage = false)
	{
		$table = parent::getBasicTable(self::$route, $systemPage);
		// Type | Name | LocationName | Latency | Lastseen | IP Address
		$table = $table	->addColumn(
								'name',
								'updated',
								trans('admin.info'),
								trans('admin.created-updated'),
								trans('admin.actions')
								 )
						->setOptions( [
								'order' => [2, "asc"],
								'aoColumns' => [
									['bVisible' => false],
									['bVisible' => false],
									['aTargets' => [ 2 ], 'aDataSort' => [ 0 ]],
									['aTargets' => [ 3 ], 'aDataSort' => [ 1 ]],
									// Actions column will only be visible if you have the right permissions
									['sWidth' => '60px', 'bSortable' => false]
								]
							]);

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
	public static function makeTable($query, $system = false)
	{
	    $route = self::$route;
	    if ($system)
        {
            $route = 'networking/' . $route;
        }

		return self::query( $query )
			->showColumns( 'name', 'updated', 'info', 'created-updated', 'actions')
			->addColumn( 'info',	    	function( $gateway ) use ($route) {
				$return = '<a href="/'. $route . '/' . $gateway->id .'">';
				$return .= '<div class="detail-column"><span class="detail-column-description name"><strong>' . $gateway->name .'</strong></span></div>';
				$return .= '<div class="detail-column"><span class="detail-column-description mac">' . $gateway->mac .'</span></div>';
				$return .= '<div class="detail-column"><span class="detail-column-description ip">' . $gateway->ip .'</span></div>';
				$return .= '<div class="detail-column"><span class="detail-column-title"><strong>'.trans('admin.latency').'</strong>: </span><span class="detail-column-description ">' ;

				// If packetloss is 100 then the latency is N/A
				if($gateway->packetloss == 100)
					$return .= trans('admin.n-a');
				else
					$return .= $gateway->latency;

				$return .= '</span></div>';

				$return .= '<div class="detail-column"><span class="detail-column-description type">' . $gateway->type .'</span></div>';
				$return .= '<div class="detail-column"><span class="detail-column-description site">' . $gateway->site .'</span></div>';
				$return .= '</a>';
				return $return;
			} )
			->addColumn( 'created-updated',	    	function( $gateway ) use ($route) {
				$return = '<a href="/'. $route . '/' . $gateway->id .'">';
				$return .= '<span class="detail-column-description hidden-updated"><span style="display: none">' . $gateway->updated .'</span></span>';
				$return .= '<div class="detail-column"><span class="detail-column-title"><strong>'.trans('admin.updated').'</strong>: </span><span class="detail-column-description ">' . DateTime::medium($gateway->updated, true) .'</span></div>';
				$return .= '<div class="detail-column"><span class="detail-column-title"><strong>'.trans('admin.created').'</strong>: </span><span class="detail-column-description ">' . DateTime::medium($gateway->created, true) .'</span></div>';
				$return .= '</a>';
				return $return;
			} )

			->addColumn( 'actions',			function( $gateway ) use ($route)
			{
				$actions = '';

				$actions .= '<a title="' . trans('admin.view') . ' '.$gateway->name.'" class="action action_view" href="/'. $route . '/' . $gateway->id.'" data-id="'.$gateway->id.'" data-name="'.$gateway->name.'" ><i class="fa fa-eye action text-info"></i></a>';

				//checking gateways.edit permission to show the edit link or not
				if (self::canAccess($route, 'edit'))
				{
					$actions .= '<a title="' . trans("admin.edit") . ' \'' . $gateway->name. '\'" 	class="action action_edit" href="/'. $route . '/' . $gateway->id . '/edit"><i class="fa fa-pencil action text-info"></i></a>';
				}

				//Checking gateways.delete permission to show the delete link or not
				if (self::canAccess($route, 'delete'))
				{
					$actions .= '<a title="' . trans("admin.delete") . ' \'' .$gateway->name . '\'" class="action action_delete" href="/'. $route . '/' . $gateway->id  . '"  data-id="'.$gateway->id.'" data-name="'.$gateway->name.'" ><i class="fa fa-trash-o action text-danger"></i></a>';
				}

				return $actions;
			})
			->searchColumns	( 'gateway.mac', 'gateway.site', 'gateway.type' ,'gateway.name',  'hardware.nasid', 'gateway.updated:char:255' , 'hardware.extip','hardware.ip', 'site.name' )
			->orderColumns 	( 'name',  'updated' )
			->make();
	}



    /**
     * @param bool $clientSide
     * @param bool $systemPage
     * @return \Illuminate\Database\Query\Builder
     */
    public static function getGateways($clientSide = false , $systemPage = true)
    {
        //$systemSites = [session('admin.site.loggedin')];
        $systemSites = session('admin.site.children');

        // Create the query to run the datatable
        // Type | Name | LocationName | Latency | Lastseen | IP Address
        $gateways = \DB::table( 'airconnect.gateway' )
            //	->orderBy('mac')
            ->select(
                'gateway.mac as mac',
                'gateway.id as id',
                'gateway.type as type',
                'gateway.name as name',
                'hardware.nasid as nasid' ,
                'hardware.ip as ip' ,
                'gateway.created as created' ,
                'hardware.latency as latency' ,
				'hardware.packetloss as packetloss' ,
                'hardware.updated as updated' ,
                'hardware.updated as hidden-updated' ,
                'site.name as site',
                'gateway.status as status')
            ->leftJoin('site', function ($join) {
                $join->on('site.id', '=', 'gateway.site');
            })
            ->leftJoin('airhealth.hardware', function ($join) {
                $join->on('gateway.mac', '=', 'hardware.mac');
            })
            ->where( 'gateway.status','!=', 'deleted' )
            ->whereIn('gateway.type', array_keys(\App\Models\AirConnect\Gateway::getGatewayTypes()));

        // System page, show all gateways under your loggedin site's children
        if($systemPage)
            $gateways = $gateways->whereIn( 'gateway.site', $systemSites );

        // Special code for client side datatable
        if ($clientSide)
            $gateways = $gateways->get();

        return $gateways;
    }
}