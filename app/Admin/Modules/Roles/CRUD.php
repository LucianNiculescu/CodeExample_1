<?php
namespace App\Admin\Modules\Roles;

use \App\Admin\Helpers\BasicCRUD;
use App\Models\AirConnect\Permission as PermissionModel;
use App\Admin\Helpers\Rules;
use App\Admin\Modules\Roles\Logic as Roles;
use App\Models\AirConnect\RoleWidget as RoleWidgetModel;
use App\Models\AirConnect\Widget as WidgetModel;
use App\Admin\Modules\Permissions\Logic as Permissions;

class CRUD extends BasicCRUD
{
	public $rules =
		[
			'role'	=>	Rules::REQUIRED_NAME,
		];


	/**
	 * CRUD constructor.
	 * Constructing the needed Model
	 * @param $model
	 */
	public function __construct($model)
	{
		parent::__construct($model);
		$this->successMsg = trans('admin.role-saved');

	}

	/**
	 * Creating a new Role
	 */
	public function create($systemPage = false)
	{
		// Creating the role
		$this->requestData['status'] = 'active';
		parent::create();

		// Saving role-manage permission data for current users and its parent roles
		$this->saveRoleManagePermission($this->modelObject['id'], $systemPage);
		$this->saveRoleWidgets($this->modelObject['id'], true);
		Permissions::save(call_user_func_array('array_merge', $this->requestData['permission']), $this->modelObject['id']);
	}

	/**
	 * Updating existing Role and its default widgets
	 * @param $id
	 */
	public function update($id, $systemPage = false)
	{
		$requestData = \Request::all();
		// Updating the Role
		parent::update($id);
		$this->saveRoleWidgets($id);

		if(isset($requestData['permission']))
		{
			if(isset($requestData['permission'][$id]))
			{
				foreach($requestData['permission'][$id] as $permission=>$value)
				{
					if(substr( $permission, 0, 12 ) === "role-manage." and $value === '1')
					{
						// Saving role-manage permission data for role being edited and its parent roles
						$this->saveRoleManagePermission(substr( $permission, 12 ), $systemPage, $id);
					}
				}
			}
		}

		Permissions::save($this->requestData['permission']);
	}

	/**
	 * Saving Role_Widgets Data
	 * @param $roleId
	 */
	public function saveRoleWidgets($roleId, $skipDelete = false)
	{
		if(!$skipDelete)
		{
			// Delete Existing Role_Widgets Data
			RoleWidgetModel::where('role_id', $roleId)
				->delete();
			$formPermissions = $this->requestData['permission'][$roleId];
		}
		else
			$formPermissions = call_user_func_array('array_merge', $this->requestData['permission']);


		// Getting all Widgets information from the DB
		$allWidgets = WidgetModel::get()->toArray();

		// Getting all widgets permissions from the form
		$widgetsPermission = array_filter ( $formPermissions, function($key){
			return substr( $key, 0, 8 ) === "widgets.";
		}, ARRAY_FILTER_USE_KEY);

		$order = 1;
		$newRoleWidgets = [];

		// Loop in all widgets to get its information to be saved in the role_widget table
		foreach ($allWidgets as $widget)
		{
			// Getting the order from the submitted form
			$permission = 'widgets.'.$widget['title'];
			$order = array_search($permission, array_keys($widgetsPermission)) + 1;
			$widgetPermission = $widgetsPermission[$permission] ?? null;
			if($widgetPermission == 2)
			{// Allowed and default (active)
				$routes = explode(',', $widget['routes']);
				foreach($routes as $route)
				{
					$newRoleWidgets[] = [
						'role_id' 	=> $roleId,
						'widget_id' => $widget['id'],
						'route'		=> $route,
						'order'		=> $order,
						'status'	=> 'active'
					];
				}
			}
			elseif($widgetPermission == 1)
			{// Allowed only (inactive)
				$routes = explode(',', $widget['routes']);
				foreach($routes as $route)
				{
					$newRoleWidgets[] = [
						'role_id' 	=> $roleId,
						'widget_id' => $widget['id'],
						'route'		=> $route,
						'order'		=> $order,
						'status'	=> 'inactive'
					];
				}
			}
		}

		RoleWidgetModel::insert($newRoleWidgets);
	}

	/**
	 * Saving Role-Manage permission for the role that created this role and all its parent roles
	 * @param $roleId
	 * @param $system
	 * @param null $currentUserRole
	 */
	public function saveRoleManagePermission($roleId, $system, $currentUserRole = null)
	{
		$roleManagePermissions = [];

		// If no $currentUserRole passed used the one from the current user
		if(is_null($currentUserRole))
			$currentUserRole = session('admin.user.role_id');

		// Getting parent of current role
		$rolesToUpdate = Roles::getAllParentRoles($currentUserRole, $system);

		// Including current
		$rolesToUpdate = array_unique (array_merge ($rolesToUpdate, [$currentUserRole]));

		// Building the insert array
		foreach($rolesToUpdate as $roleToUpdate)
		{
			if($roleId != $roleToUpdate and !in_array($roleId, Roles::$allParentsWithChildren[$roleToUpdate] ?? []))
				$roleManagePermissions[] = [ 'role_id' => $roleToUpdate, 'permission' => 'role-manage.' . $roleId ];
		}

		// Inserting permission data for role-manage.role_id data
		PermissionModel::insert($roleManagePermissions);
	}

	/**
	 * Getting Roles for the datatable
	 * @param $systemPage
	 * @return mixed
	 */
	public static function getRolesForDatatable($systemPage)
	{
		$result = \DB::table( 'roles' )
					->orderBy('roles.role')
					->select('roles.id as id', 'roles.role as name','roles.description', 'site.name as site')
					->whereIn('roles.id' ,array_keys(Roles::getManagedRoles(session('admin.user.role_id'), $systemPage)));

		// If system page, then inner join site, otherwise leftjoin site
		if($systemPage)
			$result = $result->join('site', function ($join) {
				$join->on('site.id', '=', 'roles.site_id');
			});
		else
			$result = $result->leftJoin('site', function ($join) {
				$join->on('site.id', '=', 'roles.site_id');
			});


		return $result->get();
	}
}