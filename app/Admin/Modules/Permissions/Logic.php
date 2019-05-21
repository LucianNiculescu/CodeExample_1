<?php

namespace App\Admin\Modules\Permissions;


use App\Models\AirConnect\Permission as PermissionModel;
use App\Admin\Modules\Roles\Logic as Roles;
use App\Admin\DevTools\Cache\Controller as Cache;

class Logic
{
	/**
	 * Returns all user permissions
	 * sending roles as an array and will return all their permissions
	 * @param $roleIds
	 * @return mixed
	 */
	public static function getPermissions($roleIds)
	{
		return PermissionModel::select('role_id','permission')
							->whereIn('role_id', $roleIds)
							->get()
							->toArray();
	}

	/**
	 * Saves all permissions
	 * @param array $data comes from the roles and permissions form
	 */
	public static function save(Array $data, $newId = null)
	{
		$newDataSet = [];

		if(is_null($newId))
		{
			$currentRole = key($data);
			// Deleting all permissions for the current role
			PermissionModel::where('role_id', $currentRole)->delete();
			$formPermissions = $data[$currentRole];
		}
		else
		{
			$currentRole = $newId;
			$formPermissions = $data;
		}

		// Building the new permission data
		foreach ($formPermissions as $newPermission => $value)
			if($value > 0)
				$newDataSet[] = [
					'role_id' => $currentRole,
					'permission' => strtolower($newPermission),
				];

		//inserting all new permissions
		PermissionModel::insert($newDataSet);

		// Clearing Permissions from Cache
		Cache::clearPermissions();
	}
}