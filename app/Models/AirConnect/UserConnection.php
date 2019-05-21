<?php
namespace App\Models\AirConnect;
use App\Models\BaseModel;

class UserConnection extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'user_connection';

	/**
	 * user_connection belongs to site
	 * @return mixed
	 */
	public function userConnectionSite()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'id' );
	}

	/**
	 * user_connection belongs to user
	 * @return mixed
	 */
	public function userConnectionUser()
	{
		return $this->belongsTo( '\App\Models\AirConnect\User', 'id' );
	}
}