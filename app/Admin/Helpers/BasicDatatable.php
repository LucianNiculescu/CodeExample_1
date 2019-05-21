<?php


namespace App\Admin\Helpers;

use Gate;

/**
 * Abstracted class that will setup the datatable with the basic options
 * Class Datatable
 * @package App\Admin\Helpers
 */
class BasicDatatable extends \Datatable
{

	public static $datatableRoute = '';
	public static $fullRoute = '';

	/**
	 * Setting up datatable and send it back
	 * @return mixed
	 */
	public static function getBasicTable($route, $systemPage = false, $parameter = null)
	{
		// Calculating the route for the datatable depending of the systemPage flag and module name
		if (!$systemPage)
		{
			self::$datatableRoute = 'datatable.'. $route;
		}
		else
		{
			self::$datatableRoute = 'datatable.system.' . $route ;
		}

		self::$fullRoute = route(self::$datatableRoute, $parameter);   							// this is the route where data will be retrieved

		return self::table()
			->setUrl(self::$fullRoute)
			->setClass('table table-striped table-bordered hover')
			->setOptions( [
				'oLanguage' => [
					"oPaginate" =>
						["sFirst" 		=> trans('admin.datatable-paginate-first'),
						"sPrevious" 	=> trans('admin.datatable-paginate-previous'),
						"sNext" 		=> trans('admin.datatable-paginate-next'),
						"sLast" 		=> trans('admin.datatable-paginate-last'),
						],
					"sInfo"				=> trans( 'admin.datatable-info' ),
					"sInfoEmpty"		=> trans( 'admin.datatable-infoEmpty' ),
					"sSearch"			=> trans( 'admin.datatable-search' ),
					'sEmptyTable' 		=> trans( 'admin.datatable-emptyTable' ),
					'sLengthMenu' 		=> trans( 'admin.datatable-lengthMenu' ) .' _MENU_ ' .trans( 'admin.datatable-infoPostFix' )
				],
				'searchDelay' => 9600
			])
			// Showing the loading_datatable div while loading the data
			->setCallbacks(	'fnPreDrawCallback', 'function (  ) {
				showLoadingDiv($(this));					
			}')
			// Hiding the loading_datatable after showing the data
			->setCallbacks(	'fnDrawCallback', 'function (  ) {
				hideLoadingDiv($(this));
			}')
			->setCallbacks(	'fnInitComplete', self::getInitComplete(array('setupSearchDelay(oSettings );')));
	}


	/**
	 * Building initComplete callback from a list of calls (as strings) and returning the full function code
	 * @return mixed
	 */
	public static function getInitComplete($initFunctions)
	{
		// build function header
		$initComplete = 'function ( oSettings, json ) {';
		foreach ($initFunctions as $initFunction)
		{
			$initComplete .= $initFunction . "\r";
		}
		// terminate the function
		$initComplete .= '}';

		return $initComplete;
	}


	/**
	 * Checks permissions to show the Actions column or not
	 * @param $route
	 * @param array $customActions
	 * @return bool
	 */
	public static function showActions($route, $customActions = ['activate', 'delete', 'edit'])
	{
		$route = ltrim($route, '/');
        $route = str_replace('/' , '.' , $route);

        // Looping into $customActions if there is anyone of them is true then the function will return true
        foreach ($customActions as $action)
		{
			if($action == 'view')
			{
				if(Gate::allows('access' , $route ) )
					return true;
			}
			else
			{
				if(Gate::allows('access' , $route . '.' . $action ) or Gate::allows('access' , $route . '-' . $action ))
					return true;
			}
		}

        // After looping it will return false if no permission found
		return false;
	}


	/**
	 * Can Access is a generic check if the user can access a permission or not
	 * @param $route
	 * @param $permission
	 * @return mixed
	 */
	public static function canAccess($route, $permission)
	{
		$route = ltrim($route, '/');

		$route = str_replace('/' , '.' , $route);
		return Gate::allows('access' , $route . '.' . $permission );
	}

}