<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Admin\Search\Searchable;

class User extends BaseModel  implements
	AuthenticatableContract,
	AuthorizableContract,
	CanResetPasswordContract
{
	// Can be indexed to elasticsearch
	use Authenticatable, Authorizable, CanResetPassword, Searchable;

	protected $connection = 'airconnect';
	public $table = 'user';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'site', 'user', 'name', 'type', 'password', 'mac', 'status'
	];

	/**
	 * user belongs to site
	 * @return mixed
	 */
	public function userSite()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'site' );
	}

    /**
     * Get the blocked MAC address records for the Site.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function blocked()
    {
        return $this->hasMany( '\App\Models\AirConnect\Site', 'site');
    }

	/**
	 * Do a join on the token table, foreign key is email.
	 * user can have many token
	 * @return mixed
	 */
	public function tokens()
	{
		return $this->hasMany( '\App\Models\AirConnect\Token', 'email' );
	}

	/**
	 * Do a join on the transaction table, foreign key is user.
	 * user can have many transaction
	 * @return mixed
	 */
	public function transactions()
	{
		return $this->hasMany( '\App\Models\AirConnect\Transaction', 'user' );
	}

	/**
	 * Do a join on the user_attribute table, foreign key is ids.
	 * user can have many user_attribute
	 * @return mixed
	 */
	public function attributes()
	{
		return $this->hasMany( '\App\Models\AirConnect\UserAttribute', 'ids' );
	}

	/**
	 * Do a join on the user_connection table, foreign key is ids.
	 * user can have many user_connection
	 * @return mixed
	 */
	public function userConnections()
	{
		return $this->hasMany( '\App\Models\AirConnect\UserConnection', 'ids' );
	}

	public static function getGuestTypes()
	{
		return [];
	}
}