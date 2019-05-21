<?php

namespace App\Admin\Modules\Roles;

use App\Models\AirConnect\Role as RoleModel;
use App\Models\AirConnect\Permission as PermissionModel;
use App\Models\AirConnect\Widget as WidgetModel;

use App\Models\AirConnect\RoleWidget as RoleWidgetModel;


class Logic
{
	/**
	 * Loads all Roles and permissions from Role with an eager loading
	 * It prepares an array with a key of role and responsibilities , user Permissions and category & permissions
	 * @return array
	 */

	public static $roles = []; 							// Array with Key as id and value role

	public static $allParentsWithChildren = []; 					// Array of All Parents to avoid hitting the DB
	public static $knownParents = []; 					// Array of Known parents to avoid endless loop
	public static $keptRoles = [0]; 					// Array of user Role + Dev Role + Roles that are not managed by the current Role
	public static $rolesToManage = []; 					// Array of Roles to Manage
	public static $parentRoles	 = []; 					// Array of Roles are parents to current role i.e. can manage it
	public static $rolesNotToManage = []; 				// Array of Roles not to Manage
	public static $rolesWithPermissions = []; 			// Array with Key as role and value as an array of permissions
	public static $rolesWithExplodedPermissions = []; 	// Array with Key as role and value as an array of permissions exploded
	public static $userPermissions = [];				// Array of user Permissions
	public static $categoryWithPermissions = [];		// Array of the Category as Key and value is an array of Permissions
//	public static $lonelyCategories = []; 				// Array of Permissions with no dots, i.e. category only ex:"CatOnly"
//	public static $categoriesAsRouteAndDots = []; 		// Array of Permissions that uses also a category only ex:"role , role.edit , role.create"
	/**
	 * Setup function to prepare the above arrays to be sent to the roles and permissions view
	 */
	public static function setup($userRole, $currentRole, $systemPage = false)
	{
		// The order of calling these functions is important
		self::setupRoleWithPermissions($userRole, $currentRole, $systemPage);
		return self::setupCategoriesPermissions();
		//self::setupCategoryOnlyArrays();
	}

	/**
	 * Setup the view only mode for roles
	 */
	public static function listPermissions($userRole, $currentRole, $systemPage = false)
	{
		$result 	= [];
		$allRoles	= RoleModel::pluck('role', 'id')->toArray();
		$permissions = self::setupRoleWithPermissions($userRole, $currentRole, $systemPage)[$currentRole];

		foreach ($permissions as $permission)
		{
			$details = explode('.', $permission);
			if(sizeof($details) == 1)
			{
				$result[$details[0]][] = trans('admin.view');
			}
			else
			{
				if(is_numeric($details[1]))
				{
					$result[$details[0]][] = $allRoles[$details[1]];
				}
				else
				{
					$result[$details[0]][] = trans('admin.'. $details[1]);
				}
			}
		}
		return $result;
	}

	/**
	 * Getting all parent roles, i.e. roles that can manage the passed role and their managers too
	 * @param $userRole
	 * @param $system
	 * @return array
	 */
	public static function getAllParentRoles($userRole, $system)
	{
		// Filling $allParentsWithChildren
		if(empty(self::$allParentsWithChildren))
		{
			// Getting all role-manage permission for the current user
			$parents = PermissionModel::where('permission' , 'like' , 'role-manage.%');

			// if system flag then get all roles in the estate plus the roles with site_id = null
			if($system)
			{
				$rolesInEstate = self::getRolesInEstate(true);
				$parents = $parents->whereIn('role_id', array_keys($rolesInEstate));
			}

			$parents = $parents->get()->toArray();

			// Will store all Parents and their role-manage roles
			foreach ($parents as $parent)
			{
				self::$allParentsWithChildren[$parent['role_id']][] = substr($parent['permission'], 12);
			}
		}

		self::getParentRoles($userRole, $system);
		return self::$parentRoles;
	}

	/**
	 * Recursively get parent roles
	 * @param $userRole
	 * @param $system
	 */
	public static function getParentRoles($userRole, $system)
	{
		$parents = [];
		// Saving userRole to KnownParents to avoid endless recursion
		self::$knownParents[] = $userRole;

		// Looping in all Parents and if the userRole is in the children then add this parent to knownparents
		foreach (self::$allParentsWithChildren as $parentRole => $childrenRoles)
		{
			// If the role is in the childrenRoles then add the $parentRole to $parents
			if(in_array($userRole, $childrenRoles))
				$parents[] = $parentRole;
		}

		// Add to ParentRoles unique roles
		self::$parentRoles = array_unique (array_merge (self::$parentRoles, $parents));

		// Recursively get paretroles
		foreach ($parents as $parent)
		{
			if(!in_array($parent, self::$knownParents))
				self::getParentRoles($parent, $system);
		}
	}

	/**
     * Getting all roles that user can manage
     * @return mixed
     */
    public static function getManagedRoles($userRole, $system)
    {
        // Getting all role-manage permission for the current user
        $result = PermissionModel::where('role_id', $userRole)
            ->where('permission' , 'like' , 'role-manage.%')
            ->select('permission')
            ->get()
            ->toArray();

        // if system flag then get all roles in the estate plus the roles with site_id = null
        if($system)
        {
            $rolesInEstate = self::getRolesInEstate(true);
        }

        // Putting only the role_id in the $rolesToManage array by cutting 12 characters from the permission i.e. cutting 'role-manage.'
        foreach ($result as $rows) {
            foreach ($rows as $row)
            {
                $roleId = substr($row, 12);
                if ($roleId != $userRole)
                {
                    if ($system)
                    {
                        // If in the $rolesInEstate then add it to the results
                        if (in_array($roleId, array_keys($rolesInEstate)))
                        {
                            self::$rolesToManage[$roleId] = $roleId;
                        }
                    }
                    else
                    {
						self::$rolesToManage[$roleId] = $roleId;
                    }
                }
            }
        }

        return self::$rolesToManage;
    }

	/**
	 * setting up rolesWithPermission Array
	 * Key is role id
	 * Value Array of Permissions
	 */
	public static function setupRoleWithPermissions($userRole, $currentRole, $systemPage)
	{
		$result = [];

		$sites = session('admin.site.estate');

		// Setting up the roles to manage
		self::getManagedRoles($userRole, $systemPage);

        $rolesAndPermissions =  RoleModel::orderBy('role')
            ->with('permissions');

		$rolesAndPermissions =  $rolesAndPermissions
			->whereIn( 'id', [$userRole, $currentRole] );

        $rolesAndPermissions = $rolesAndPermissions->get()->toArray();

		// Looping into the result from DB to setup the rolesWithPermissions array
		foreach($rolesAndPermissions as $rolesAndPermission)
		{
			$roleId 	= $rolesAndPermission['id'];
			$roleName 	= $rolesAndPermission['role'];
			$permissions = $rolesAndPermission['permissions'];
			$tempArray = [];

			// Gathering the permissions per role_id
            foreach ($permissions as $permission)
            {
               	$tempArray[] = $permission['permission'];
            }

            // Fill only the managed roles
            //if(!$systemPage or in_array($roleId, self::$rolesToManage) or $roleId == $userRole)
            if(in_array($roleId, self::$rolesToManage) or $roleId == $userRole )
            {
                // Create an array with a Key as role_id and value as an array of permissions
                $result[$roleId]        = $tempArray;
                self::$roles[$roleId]   =  $roleName;
            }
		}

		if(isset($result[$userRole]))
		{
			// They key is the role id, so if you pass 0 you will get DevPermissions
			self::$userPermissions = $result[$userRole];
		}

		// Remove Dev permissions and User's permissions
		if(!is_null($currentRole))
		{
			//excluding Dev Role
			array_forget($result,0);
			array_forget(self::$roles,0);

			// Excluding the current user role
			array_forget($result,$userRole);
			array_forget(self::$roles,$userRole);
		}

		self::$rolesWithPermissions = $result;

		return $result;
	}

	/**
	 * Sets up the $categoryWithPermissions array
	 * Key is the category name
	 * Value is permission with dots
	 */
	private static function setupCategoriesPermissions()
	{
		$result = [];

		// Looping into userPermissions and getting the category as the key and the value as an array of permissions
		foreach(self::$userPermissions as $userPermissionWithDots)
		{
			// checking if the permission has a menu category or not, it is separated in the DB with |

			$userCategoryPermission = explode ('.',$userPermissionWithDots,2);
			$userCategory = $userCategoryPermission[0];


			// if the permission is without a dot then it is a category only
			if(count($userCategoryPermission) > 1)
			{
				$userPermission = $userCategoryPermission[1];
			}
			else
			{
				$userPermission = '';
			}

			if(is_numeric($userPermission))
            {
                // If the permission is a number this means it is a role-manage permission
                // Add it if the role is in the roles to manage array
                if (in_array($userPermission, self::$rolesToManage))
                {
                    $result[$userCategory][] = $userPermission;
                }
            }
            else
            {
                $result[$userCategory][] = $userPermission;
            }
		}

		self::$categoryWithPermissions = $result;
		return $result;

	}

	/**
	 * Checks the widgets array types and return an array of widget arrays for [estate, company, site]
	 * @param $widgetsWithRoleWidget
	 * @return array
	 */
	public static function reArrangeWidgets($widgetsWithRoleWidget)
	{
		$resultWidgets = [];
		// Temporary order
		$tempCount = 1;
		foreach ($widgetsWithRoleWidget as $widgetWithRoleWidget)
		{
			// If it has a role_widget , store the order otherwise increase the tempCount
			if(!empty($widgetWithRoleWidget['role_widget']))
			{
				$count = 0;
				foreach($widgetWithRoleWidget['role_widget'] as $roleWidget)
				{
					$widgetOrder = $roleWidget['order'];
					$count++;
					$resultWidgets[$widgetOrder] = $widgetWithRoleWidget;
				}
			}
			else
			{
				// Nothing in the roleWidget table, so do a tempCounter
				$widgetOrder = 'temporder'.$tempCount++;
				$resultWidgets[$widgetOrder] = $widgetWithRoleWidget;
			}
		}

		// Arranging the arrays
		ksort($resultWidgets);

		$result = [];
		array_walk($resultWidgets, function(&$item, $key ) use (&$result){
			$result[$item['title']] = $item;
		});

		return $result;

	}

	/**
     * Getting all roles for all users page
     * Also gets roles that the current user can manage and in his estate in the system>users page
     * @param $userRole
     * @param bool $system
     * @return array
     */
    public static function getRolesForUsers($userRole , $system = false)
    {
        // Getting id and role name and site id
        $result = RoleModel::select('id', 'role' , 'site_id')
            ->where('id' ,'!=' , 0);

		$rolesToManage = self::getManagedRoles($userRole, $system);

        $result = $result->whereIn('id' , $rolesToManage)
			->get()
            ->toArray();

        $roles = [];

        // Setting up the dropdownlist as the key will be the role id and the value would be the role name and the site id between brackets
        foreach ($result as $row)
        {
            $roles[$row['id']] = $row['role']. ($row['site_id'] ? ' (' . $row['site_id'] .')' : '');
        }

        return $roles;
    }

	/**
	 * Widgets for current permission level
	 * Uses the role id to get all widgets linked to the role
	 * @param $roleId
	 * @return mixed
	 *
	 */
    public static function getAllowedWidgets($roleId = null)
	{
		if(is_null($roleId))
			$roleId = session('admin.user.role_id');

		// Getting an array of the widgets allowed to the current role_id
		$allowedWidgets = PermissionModel::where('role_id', $roleId)
			->where('permission', 'like', 'widgets.%')
			->get()
			->pluck('permission')
			->map(function($permission){ return explode('.', $permission)[1];})
			->toArray();

		// Searching the widgets for the widget title and getting its role_widget data too
		$widgetsWithRoleWidgets = self::getWidgetsWithRoleWidgets($allowedWidgets, $roleId);

		return $widgetsWithRoleWidgets;
	}


	/**
	 * Getting all Widgets and their types and routes
	 * @return array
	 */
    public static function getAllWidgets($widgets = [])
	{
		$widgetIds = [];
		foreach($widgets as $widget)
		{
			$widgetIds[] = $widget['id'];
		}

		$allWidgets = [];
		$widgets = WidgetModel::select('title', 'type', 'routes', 'description')
			->whereIn('id', $widgetIds)
			->get()->toArray();

		foreach($widgets as $widget)
		{
			$allWidgets[$widget['title']]['type'] 			= isset($allWidgets[$widget['title']]['type']) ? $allWidgets[$widget['title']]['type']. ',' . $widget['type'] : $widget['type'] ;
			$allWidgets[$widget['title']]['routes'] 		= $widget['routes'];
			$allWidgets[$widget['title']]['description'] 	= $widget['description'];
		}
		return $allWidgets;
	}


	/**
	 * Search the widget table with an array of widget names to see if it exists
	 * NOTE: $roleId is the role of the current page
	 * @param $widgets
	 * @return mixed
	 */
	private static function getWidgetsWithRoleWidgets($widgets, $roleId)
	{
		$resultArray = [];

		$widgetsWithRoleWidgets = WidgetModel::whereIn('title', $widgets)
			->with(['RoleWidget' => function($q) use ($roleId){
				$q->where('role_id', $roleId);
			}])
			->get()
			->toArray();

		// Handling multiple routes per widget
		array_map(function($widget) use(&$resultArray){
			$explodedRoutes = explode(',', $widget['routes']);

			// If the widget has many routes i.e. comma separated values
			if( count($explodedRoutes) > 1)
			{
				// Explode them and create a new item per widget with the new route
				foreach ($explodedRoutes as $route)
				{
					$widget['routes'] = $route;
					$resultArray[] = $widget;
				}
			}
			else
				$resultArray[] = $widget;
		}, $widgetsWithRoleWidgets);


		// Array Map to remove un-related role_widgets
		$resultArray = array_map(function($widgetWithRoleWidget){
			$currectRoute = $widgetWithRoleWidget['routes'];

			// If there is many role_widgets we need to delete the ones that is not related to the route of the current widget
			if(count($widgetWithRoleWidget['role_widget']) > 1)
				// Looping in the role_widget array that comes from the widgets->with('role_widget')
				foreach($widgetWithRoleWidget['role_widget'] as $key=>$roleWidget)
					// If the role_widget has different route than the one we are handling then remove it
					if($roleWidget['route'] != $currectRoute)
						// Removing un-related role_widget since it has a wrong order and is causing issues
						unset($widgetWithRoleWidget['role_widget'][$key]);

			// Rebasing the array $widgetWithRoleWidget['role_widget'] because some items were removed
			$widgetWithRoleWidget['role_widget'] = array_values($widgetWithRoleWidget['role_widget']);
			return $widgetWithRoleWidget;
		},$resultArray);

		return $resultArray;
	}


	/**
	 * Getting roles
	 * @param $systemPage
	 * @return mixed
	 */
	public static function getRoles($systemPage)
	{
		$allRoles = RoleModel::with(['site' => function($query){
			$query->select('id','name');
		}])
			->select('id', 'role', 'site_id')->get()->keyBy('id')->toArray();

		$roles = self::getManagedRoles(session('admin.user.role_id'), $systemPage);

		foreach ($roles as $roleKey => $roleValue)
			$roles[$roleKey] = $allRoles[$roleKey];

		return $roles;
	}

	/**
	 * Getting all roles in your estate
	 * @return mixed
	 */
	public static function getRolesInEstate($includeNullSiteId)
	{
		$sites = session('admin.site.estate');
		$result = RoleModel::whereIn('site_id', $sites);

		if ($includeNullSiteId)
			$result = $result->orWhere('site_id', null);

		$result = $result->pluck('role', 'id')->toArray();

		return $result;
	}
}