<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Token extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'token';
	public $timestamps = false;
	protected $fillable = array('email', 'token', 'created');

	/**
	 * token belongs to user
	 * @return mixed
	 */
	public function tokenUser()
	{
		return $this->belongsTo( '\App\Models\AirConnect\User', 'user' );
	}
}