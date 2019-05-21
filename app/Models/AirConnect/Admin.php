<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Models\AirConnect\Permission as PermissionModel;

class Admin extends BaseModel  implements
	AuthenticatableContract,
	AuthorizableContract,
	CanResetPasswordContract
{
	use Authenticatable, Authorizable, CanResetPassword;
	public $table = 'admin';
	protected $hidden = ['password'];
	protected $fillable = ['site', 'adminId', 'role_id', 'template_id',  'username',  'password', 'email_reports', 'language', 'timezone', 'status', 'creator' ];
	protected $guarded = [ 'password' ];
	protected $connection = 'airconnect';

	/**
	 * Do a join on the admin_attribute table, foreign key is ids.
	 * admin can have many admin_attribute
	 * @return mixed
	 */
	public function adminAttributes()
	{
		return $this->hasMany( '\App\Models\AirConnect\AdminAttribute', 'ids' );
	}

	/**
	 * Do a join on the blocked table, foreign key is blocker.
	 * admin can have many blocked
	 * @return mixed
	 */
	public function blockeds()
	{
		return $this->hasMany( '\App\Models\AirConnect\Blocked', 'blocker' );
	}

	/**
	 * Do a join on the messages table, foreign key is user_id.
	 * admin can have many messages
	 * @return mixed
	 */
	public function messages()
	{
		return $this->hasMany( '\App\Models\AirConnect\Message', 'user_id' );
	}

	/**
	 * Do a join on the roles table, foreign key is role_id.
	 * admin can have many messages
	 * @return mixed
	 */
	public function roles()
	{
		return $this->hasMany('\App\Models\AirConnect\Role', 'id' , 'role_id');		// was adminId
	}
	
	/**
	 * Do a join on the roles table, foreign key is role_id.
	 * admin can have many messages
	 * @return mixed
	 */
	public function permissions()
	{
		return $this->hasMany('\App\Models\AirConnect\Permission', 'role_id' , 'role_id'); // second was adminId
	}

	/**
	 * admin belongs to site
	 * @return mixed
	 */
	public function adminSite()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'site');
	}

	/**
	 * @return mixed
	 */
	public function adminTemplate()
	{
		return $this->hasOne( '\App\Models\AirConnect\AdminTemplate', 'id', 'template_id');
	}

	/**
	 * admin can have many widgets
	 * @return mixed
	 */
	public function adminWidget()
	{
		return $this->belongsToMany( '\App\Models\AirConnect\Widget' );
	}

	/**
	 * Getting back the user Permissions and keep it in the cache for the next time
	 * @return mixed
	 */
	public function getPermissions()
	{
		$roleId 	= $this->role_id;	//wa adminId

		$expires 	= 1; // Days the permission will expire
		$cacheKey 	= 'permissions-' . $roleId;

		if ( \Cache::has( $cacheKey ) )
		{// Use the cache
			$userPermissions = \Cache::get( $cacheKey );
		}
		else
		{// Get Permissions from DB and put it in the cache
			$userPermissions = PermissionModel::where('role_id', $roleId)->pluck('permission')->toArray();

			$expiresAt = \Carbon\Carbon::now()->addDays( $expires );
			\Cache::put( $cacheKey , $userPermissions, $expiresAt );
		}

		foreach ($userPermissions as &$userPermission)
			$userPermission = str_replace('|', '.', $userPermission);

		return $userPermissions;
	}
	
	
	/**
	 * Checking if the Admin user has a specific permission or not
	 * This will check the cache if the permission doesn't exist there it will create
	 * @param $permission
	 * @return bool
	 */
	public function hasPermission($permission)
	{
		$userPermissions = $this->getPermissions();

		return in_array($permission, $userPermissions);
	}

////////Remember_token bug workaround
	public function getRememberToken()
	{

	}

	public function setRememberToken($value)
	{

	}

	public function getRememberTokenName()
	{

	}

}