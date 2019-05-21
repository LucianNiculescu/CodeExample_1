<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class GuestWhitelist extends BaseModel
{
	protected $connection = 'airconnect';
	/**
	 * The table associated with the model
	 *
	 * @var string
	 */
	protected $table = 'guest-whitelist';

	public $timestamps = false;

	/**
	 * The attributes that are mass assignable
	 *
	 * @var array
	 */
	protected $fillable = [
		'site_id',
		'package_id',
		'guid',
		'mac',
		'performedby',
		'expires'
	];

	/**
	 * Get the Admin who created the Whitelist record
	 *
	 * @return mixed
	 */
	public function admin()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Admin', 'performedby', 'username' );
	}

	/**
	 * Get the Site where the Whitelist record belongs
	 *
	 * @return mixed
	 */
	public function site()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site');
	}

	/**
	 * Get the Site where the Whitelist record belongs
	 *
	 * @return mixed
	 */
	public function package()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Package');
	}

}