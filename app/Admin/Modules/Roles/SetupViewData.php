<?php
/**
 * Created by PhpStorm.
 * User: sheri
 * Date: 12/14/2016
 * Time: 4:47 PM
 */

namespace App\Admin\Modules\Roles;

use App\Models\AirConnect\Role as RoleModel;
use App\Admin\Modules\Sites\Logic as Sites;
use App\Admin\Modules\Roles\Logic as Roles;
use App\Admin\Helpers\BasicDatatable;


class SetupViewData
{
	/**
	 *
	 * @param bool $systemPage
	 *
	 * @return array
	 */
	public static function index($systemPage = false)
	{
		$customActions 		= ['edit', 'view'];
		$tableId = $route 	= 'roles-and-permissions';
		$title 				= trans('admin.roles-title');
		$description		= trans('admin.roles-manage-desc');

		if($systemPage)
		{
			$route 			= 'system/' . $route;
			$title 			= trans('admin.system-roles-title');
			$description	= trans('admin.system-roles-manage-desc');
		}

		$rows = CRUD::getRolesForDatatable($systemPage);

		$showActions = BasicDatatable::showActions($route, $customActions);

		$columns = [
		//	'id' 			=> '',
			'name' 			=> '',
			'description' 	=> '',
			'site' 			=> '',
		];

		return [
			'title' 		=> $title,
			'description' 	=> $description,
			'columns'		=> $columns,
			'rows' 			=> $rows,
			'route'			=> $route,
			'customActions'	=> $customActions,
			'showActions'	=> $showActions,
			'tableId'		=> $tableId,
			'clickableRow'	=> true
		];
	}


	/**
	 * Preparing the create view
	 * @param bool $systemPage
	 * @return array
	 */
	public static function create($systemPage = false)
	{
		$userSite		= session('admin.user.site');
		$loggedInSite	= session('admin.site.loggedin');

		// Setup the form's action and url
		$actionUrl = '/roles-and-permissions';
		$hiddenMethod = 'POST';

		$rolesPage		= '/roles-and-permissions';
		$title 			= trans('admin.create-new-role');
		$description	= trans('admin.role-create-desc');

		if ($systemPage)
		{
			$title 			= trans('admin.sys-create-new-role');
			$description	= trans('admin.sys-role-create-desc');
			$actionUrl	= "/system" . $actionUrl;
			$rolesPage	= "/system" . $rolesPage;
		}

		$roles = Roles::getRoles($systemPage);

		list($allWidgets, $disallowedWidgets, $inactiveWidgets, $activeWidgets) = self::getWidgetsData();


		// Data sent to the admin.roles.form page
		$data = [
			'title' 			=> $title  ,
			'description' 		=> $description,
			'hiddenMethod' 		=> $hiddenMethod ,
			'actionUrl' 		=> $actionUrl,
			'activeWidgets' 	=> $activeWidgets,
			'inactiveWidgets' 	=> $inactiveWidgets,
			'disallowedWidgets'	=> $disallowedWidgets,
			'allWidgets'		=> $allWidgets,
			'rolesPage'			=> $rolesPage,
			'userSite'			=> $userSite,
			'loggedInSite'		=> $loggedInSite,
			'systemPage'		=> $systemPage,
			'roles'				=> $roles
		];

		if ($systemPage)
		{
			$sites = Sites::getEstateNames();
			$data['sites'] = $sites;
		}

		$data += SetupViewData::permissionsPart(null, $systemPage) + ['container' => false];

		return $data;
	}


	/**
	 * Preparing the Edit View
	 * @param $id
	 * @param bool $systemPage
	 * @return array
	 */
	public static function edit($id,$systemPage = false)
	{
		$roleId = session('admin.user.role_id');

		// Redirecting to 404 if user tried to hack and edit role 0 or the current role id

		if ($id == 0 or $id == $roleId)
		{
			abort('401', trans('error.not-authorized'));
		}

		// Setup the form's action and url
		$actionUrl =  '/roles-and-permissions/'.$id;
		$hiddenMethod = 'PUT';

		$roles = Roles::getRoles($systemPage);

		if(!in_array($id, array_keys($roles)))
			abort('401', trans('error.not-authorized'));

		if($systemPage and is_null($roles[$id]['site']))
			abort('401', trans('error.not-authorized'));

		$siteSelected 	= '';
		$userSite		= session('admin.user.site');
		$loggedInSite	= session('admin.site.loggedin');

		$rolesPage		= '/roles-and-permissions';

		$title 			= trans('admin.role-edit');
		$description	= trans('admin.role-edit-desc');

		// get the Role
		$role 			= RoleModel::find($id);

		if ($systemPage)
		{
			$title 			= trans('admin.sys-role-edit');
			$description	= trans('admin.sys-role-edit-desc');
			$actionUrl		= "/system" . $actionUrl;
			$rolesPage		= "/system" . $rolesPage;
			$siteSelected 	= $role->site_id;
		}

		list($allWidgets, $disallowedWidgets, $inactiveWidgets, $activeWidgets) = self::getWidgetsData($id);

		// Data to be sent to the Role edit page
		$data =
			[
				'title' 			=> $roles[$id]['role'] ,
				'description' 		=> $description,
				'role' 				=> $role ,
				'roles'				=> $roles,
				'activeWidgets' 	=> $activeWidgets,
				'inactiveWidgets' 	=> $inactiveWidgets,
				'disallowedWidgets'	=> $disallowedWidgets,
				'allWidgets'		=> $allWidgets,
				'hiddenMethod' 		=> $hiddenMethod ,
				'actionUrl' 		=> $actionUrl,
				'rolesPage'			=> $rolesPage,
				'userSite'			=> $userSite,
				'loggedInSite'		=> $loggedInSite,
				'siteSelected'		=> $siteSelected,
				'systemPage'		=> $systemPage,
				'module'			=> $role,
			];

		if ($systemPage)
		{
			$sites = Sites::getEstateNames();
			$data['sites'] = $sites;
		}
		else
		{
			if(!is_null($roles[$id]['site']))
				$data['site'] = $roles[$id]['site']['name'];
		}

		$data += SetupViewData::permissionsPart($id, $systemPage) + ['container' => false];

		// Getting default widget names
		$data['activeWidgets'] = array_map( function($item){return substr( $item, 8 ) ;}, $data['activeWidgets']);
		$data['inactiveWidgets'] = array_map( function($item){return substr( $item, 8 ) ;}, $data['inactiveWidgets']);
		$data['disallowedWidgets'] = array_map( function($item){return substr( $item, 8 ) ;}, $data['disallowedWidgets']);

		if(isset($data['categoryWithPermissions']['widgets']))
			// Merging the original widgets with the default widgets to get the right order
			$data['categoryWithPermissions']['widgets'] = array_unique(array_merge($data['activeWidgets'], $data['categoryWithPermissions']['widgets']));

		return $data;
	}


	/**
	 * Preparing all variables to be sent to the roles page
	 * @param null $currentRole
	 * @param bool $systemPage
	 * @return array
	 */
	public static function permissionsPart($currentRole = null, $systemPage = false)
	{
		$userRole = session('admin.user.role_id');

		//	Setting up the arrays needed to show the roles and permissions page
		Roles::setup($userRole, $currentRole, $systemPage);

		// Array with Key as role and value as an array of permissions
		$rolesWithPermissions = Roles::$rolesWithPermissions;

		// Array of the Category as Key and value is an array of Permissions
		$categoryWithPermissions = Roles::$categoryWithPermissions;

		// Array of user Permissions
		$userPermissions = Roles::$userPermissions;

		// Array of all Categories
		$categories = array_keys(Roles::$categoryWithPermissions);
		sort($categories);

		// Array of Permissions with no dots, i.e. category only ex:"CatOnly"
		//$lonelyCategories = Roles::$lonelyCategories;

		$title 				= trans('admin.role-title');
		$description		= trans('admin.roles-manage-desc');
		$rolesPage			= '/roles-and-permissions';
		$actionUrl			= '/roles-and-permissions/edit';
		$createUrl			= '/roles-and-permissions/create';
		$createPermission	= 'roles-and-permissions.create';
		$editPermission		= 'roles-and-permissions.edit';

		if ($systemPage)
		{
			$title 				= trans('admin.system-role-title');
			$description		= trans('admin.system-roles-manage-desc');
			$rolesPage			= "/system" . $rolesPage;
			$actionUrl			= "/system" . $actionUrl;
			$createUrl			= "/system" . $createUrl;
			$createPermission	= "system." . $createPermission;
			$editPermission		= "system." . $editPermission;

		}
//		$categoriesAsRouteAndDots = Roles::$categoriesAsRouteAndDots;

		$data = [
			'title' 					=> $title,
			'description'				=> $description,
			'currentRolePermissions' 	=> $rolesWithPermissions[$currentRole ?? $userRole],
			'userPermissions' 			=> $userPermissions,
			'categories' 				=> $categories,
			'categoryWithPermissions' 	=> $categoryWithPermissions,
			//'lonelyCategories' 			=> $lonelyCategories,
			'actionUrl'					=> $actionUrl,
			'createUrl'					=> $createUrl,
			'rolesPage'					=> $rolesPage,
			'editPermission'			=> $editPermission,
			'createPermission'			=> $createPermission,
			'currentRole'				=> $currentRole,
		];

		return $data;
	}

	/**
	 * Setting up the widgets permissions data to display
	 * @param $id
	 * @return array
	 */
	public static function getWidgetsData($id = null): array
	{
		$activeWidgets = $inactiveWidgets = $disallowedWidgets = [];
		// Getting AllWidgets the loggedin user is allowed to use
		$usersWidgets = Roles::getAllowedWidgets(session('admin.user.role_id'));
		$allWidgets = Roles::getAllWidgets($usersWidgets);

		// If an existing role id is passed , arrange the widgets
		if(!is_null($id))
		{
			$widgetsWithRoleWidget = Roles::reArrangeWidgets(Roles::getAllowedWidgets($id));

			// Fill the widgets details in the right section (active, inactive and disallowed) which is (default, permitted, un-permitted)
			foreach ($allWidgets as $widgetName => $details)
			{
				// If the widget is in the array then it is allowed (active or inactive)
				if (in_array($widgetName, array_keys($widgetsWithRoleWidget)))
				{
					$currentWidget = $widgetsWithRoleWidget[$widgetName];
					// If it is in the role_widget table then check if it is active or not
					if (isset($currentWidget['role_widget']) and sizeOf($currentWidget['role_widget']) > 0)
					{
						if ($currentWidget['role_widget'][0]['status'] == 'active')
							$activeWidgets[$currentWidget['role_widget'][0]['order']] = 'widgets.' . $currentWidget['title'];
						else
							$inactiveWidgets[] = 'widgets.' . $currentWidget['title'];
					}
					else // If it is not in the role_widget table then it is inactive
						$inactiveWidgets[] = 'widgets.' . $currentWidget['title'];

				}
				else
					$disallowedWidgets[] = 'widgets.' . $widgetName;
			}
		}
		else
		{	// Fill all widgets in the disallowed section
			foreach ($allWidgets as $widgetName => $details)
			{
				$disallowedWidgets[] = $widgetName;
			}
		}
		ksort($activeWidgets);

		return array($allWidgets, $disallowedWidgets, $inactiveWidgets, $activeWidgets);
	}

}