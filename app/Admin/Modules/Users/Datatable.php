<?php
/**
 * Created by PhpStorm.
 * User: sheri
 * Date: 5/31/2016
 * Time: 01:41 PM
 */

namespace App\Admin\Modules\Users;

use \App\Admin\Modules\Roles\Logic as Roles;
use \App\Helpers\DateTime;
use \App\Admin\Helpers\BasicDatatable;

class Datatable extends BasicDatatable
{

	/**
	 * Setting up datatable and send it back
	 * @return mixed
	 */
	public static function getTable($systemPage = false)
	{
		// Prepare static variables to show or hide actions
		//self::prepareAccessVariables($systemPage);
        $route = 'users';

        if ($systemPage)
            $route = 'system.' . $route;
		// Calling the parent getBasicTable to setup the serverside datatable
		$table = parent::getBasicTable('users', $systemPage);

		// Setting up the columns
		$table = $table
			->addColumn(
				trans('admin.id'),
				trans('admin.role'),
				trans('admin.user-name') ,
				trans('admin.site') ,
				trans('admin.extra-info') ,
				trans('admin.language') ,
				trans('admin.time-zone') ,
				trans('admin.created') ,
				trans('admin.updated') ,
				trans('admin.actions') )       // these are the column headers
			->setOptions( [
				'aoColumns' => [
					[ 'visible' => false  ],
					null,
					null,
					null,
					[ 'bSortable' => false],
					[ 'visible' => false  ],
					[ 'visible' => false  ],
					[ 'visible' => false  ],
					[ 'visible' => false  ],
					// Actions column will only be visible if you have the right permissions
					['sWidth' => '60px'	, 'visible' =>  self::showActions($route) , 'bSortable' => false  ]
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
	public static function makeTable($query, $systemPage)
	{
		//self::prepareAccessVariables($systemPage);
        $route = 'users';

        if ($systemPage)
            $route = 'system/' . $route;

		return self::query( $query )
			//			'admin.id as id', 'roles.role as role', 'admin.username as user' ,'admin.language as lang' , 'admin.timezone as timezone' , 'admin.status as status', 'admin.created as created' ,'admin.updated as updated'
			->showColumns(  'id', 'role', 'username', 'site' ,'extraInfo', 'language' , 'timezone' , 'created' ,'updated' , 'actions' )
			->addColumn( 'id', 			function( $user ) { 	return $user->id; 	} )
			->addColumn( 'role', 		function( $user ) use ($route){ 	return '<a href="/'.$route.'/' .$user->id .'">'. 	(is_null($user->role)? trans('admin.n-a') : $user->role)			.'</a>'; } )
			->addColumn( 'username', 	function( $user ) use ($route) { 	return '<a href="/'.$route.'/' .$user->id .'">'. 	$user->username 		.'</a>'; } )
			->addColumn( 'site', 	function( $user ) use ($route) { 	return '<a href="/'.$route.'/' .$user->id .'">'. 	(is_null($user->site)? trans('admin.n-a') : $user->site) 		.'</a>'; } )

			//ExtraInfo Col is showing created updated language and timezone
			->addColumn( 'extraInfo',
				function( $user ) use ($route) {
					$extraCol = '';

					$extraCol .= '<div>';
					$extraCol .= trans('admin.created'). ' : ' . DateTime::medium($user->created);
					$extraCol .= '</div>';

					$extraCol .= '<div>';
					$extraCol .= trans('admin.updated'). ' : ' . DateTime::medium($user->updated);
					$extraCol .= '</div>';

					$extraCol .= '<div>';
					$extraCol .= trans('admin.language'). ' : ' . trans('admin.' . $user->language);
					$extraCol .= '</div>';

					$extraCol .= '<div>';
					$extraCol .= trans('admin.time-zone'). ' : ' . $user->timezone;
					$extraCol .= '</div>';

					$extraCol .= '';

					return '<a href="/'.$route.'/' .$user->id .'">'. 	$extraCol 		.'</a>';
						} )

			->addColumn( 'language', 	function( $user ) 	{ 	return trans('admin.' . $user->language); } )
			->addColumn( 'timezone', 	function( $user )	{ 	return $user->timezone; } )
			->addColumn( 'created', 	function( $user ) 	{ 	return $user->created; } )
			->addColumn( 'updated', 	function( $user ) 	{ 	return $user->updated; } )

			->addColumn( 'actions',		function( $user ) use ($route)
			{
				$actions = '';

				// if user has no permission to do any of these actions then return nothing
				if (!self::showActions($route))
				{
					return $actions;
				}
				// Configuring the color of the icon
				$statusColor = ($user->status == 'active') ? 'success' : 'danger';

				// Configuring the shape of the toogle icon
				$statusIcon  = ($user->status == 'active') ? 'on' : 'off';

				// Configuring the title of the icon
				$statusTitle = 'Click here to ' . ($user->status == 'active' ?trans('admin.de-activate') : trans('admin.activate') ).  ' \'' . $user->username ;

				// Checking sites.activation permission to show the activation link or not
				if (self::canAccess($route, 'activate'))
				{
					$actions .= '<a data-placement="left" title="' . $statusTitle . '" class="action action_status" href="/'.$route.'/'.$user->id.'" data-status="'.$user->status.'" data-id="'.$user->id.'" data-name="'.$user->username.'" ><i class="fa fa-toggle-'. $statusIcon .' action text-' . $statusColor . '"></i></a>';
				}

				//checking sites.edit permission to show the edit link or not
				if (self::canAccess($route, 'edit'))
				{
					$actions .= '<a data-placement="left" title="' . 'Edit \'' . $user->username. '\'" 	class="action action_edit" href="/'.$route.'/' .$user->id . '/edit"><i class="fa fa-pencil action text-info"></i></a>';
				}

				//Checking sites.delete permission to show the delete link or not
				if (self::canAccess($route, 'delete'))
				{
					$actions .= '<a data-placement="left" title="' . 'Delete \'' .$user->username . '\'" class="action action_delete" href="/'.$route.'/' . $user->id  . '"  data-id="'.$user->id.'" data-name="'.$user->username.'" ><i class="fa fa-trash-o action text-danger"></i></a>';
				}

				return $actions;
			})
			->searchColumns	( 'admin.id', 'roles.role', 'admin.username' , 'site.name','admin.language' , 'admin.timezone' , 'admin.created:char:255' ,'admin.updated:char:255' )
			->orderColumns 	( 'id', 'role', 'username', 'site'  )
			->make();
	}

    /**
     * Setting up the Datatable with the DB query
     * @param $systemPage
     * @return mixed
     */
	public static function setupTable($systemPage)
    {
        // Site IDs to look for
        $siteIds = [];

        $siteIds = session('admin.site.estate');

        // Create the query to run the estate datatable

        $query = \DB::table( 'admin' )
            ->leftJoin('roles', function ($join) {
                $join->on('admin.role_id', '=', 'roles.id');	// was adminId
            })
            ->leftJoin('site', function ($join) {
                $join->on('admin.site', '=', 'site.id');
            })
            //->select('admin.id as userId', 'roles.role as role', 'admin.username as username','site.name as site' ,'admin.language as language' , 'admin.timezone as timezone' , 'admin.status as status', 'admin.created as created' ,'admin.updated as updated' )
            ->select('admin.id', 'roles.role as role', 'admin.username as username','site.name as site' ,'admin.language as language' , 'admin.timezone as timezone' , 'admin.status as status', 'admin.created as created' ,'admin.updated as updated' )
            ->where( 'admin.status','!=', 'deleted' );

        if ($systemPage)
        {
            //$query = $query->whereIn( 'admin.site', $siteIds );
            $query = $query->where(function ($subquery) use ($siteIds) {
                $subquery->whereIn( 'admin.site', $siteIds )
                    ->orWhereNull('admin.site');
            });
        }
		$roles  = Roles::getRolesForUsers(session('admin.user.role_id'), $systemPage);

		$query = $query->whereIn( 'admin.role_id', array_keys($roles) );	// was adminId

        // Return the Datatable json
        return self::makeTable($query , $systemPage);
    }
}