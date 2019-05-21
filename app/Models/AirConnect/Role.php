<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;


/**
 * Class Role
 * @package App\Models\AirConnect
 * TODO: There is no Role Table in the AirConnect DB need migration
 */

class Role extends BaseModel
{
	protected $connection = 'airconnect';
	public $fillable = ['id', 'role', 'description', 'site_id', 'status'];
	/**
	 * Do a join on the role_permissions table, foreign key is role_id.
	 * @return mixed
	 */
	public function permissions()
	{
		return $this->hasMany( '\App\Models\AirConnect\Permission', 'role_id');
	}

	/**
	 * role belongs to admin
	 * @return mixed
	 */
	public function admins()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Admin', 'role_id' );	// was adminId
	}

	/**
	 * role belongs to widgets
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function widgets()
	{
		return $this->belongsToMany( '\App\Models\AirConnect\Widget');
	}

	public function site()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site');
	}
}