<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;


/**
 * Class RolePermission
 * @package App\Models\AirConnect
 * TODO: Permissions is not a table in the AirConnect DB , need migration
 */

class Permission extends BaseModel
{
	protected $connection = 'airconnect';
	public $fillable = ['role_id', 'permission'];
	public $timestamps = false;
	/**
	 * Do a join on the roles table, foreign key is role_id.
	 * @return mixed
	 */
	public function role()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Role' , 'id');
	}

	/**
	 * permissions belongs to admin
	 * @return mixed
	 */
	public function admins()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Admin' ,  'role_id' );	// was adminId
	}
}