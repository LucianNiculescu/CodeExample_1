<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Blocked extends BaseModel
{
	protected $connection = 'airconnect';
	/**
	 * The table associated with the model
	 *
	 * @var string
	 */
	protected $table = 'blocked';

	/**
	 * The attributes that are mass assignable
	 *
	 * @var array
	 */
	protected $fillable = [
		'site',
		'blocker',
		'mac',
		'reason'
	];

	/**
	 * The attributes which represent timestamps
	 *
	 * @var array
	 */
	public $timestamps = ['created'];

	/**
	 * The name of the created_at field
	 *
	 * @var string
	 */
	const CREATED_AT = 'created';

	/**
	 * Model does not have updated_at field
	 *
	 * @var string
	 */
	const UPDATED_AT = null;

	/**
	 * Get the Admin who created the Blocked record
	 *
	 * @return mixed
	 */
	public function admin()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Admin', 'blocker' );
	}

	/**
	 * Get the Site where the Blocked record belongs
	 *
	 * @return mixed
	 */
	public function parentSite()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'site' );
	}


	/**
	 * Checking if the mac is blocked in the current estate or not
	 * @param $mac
	 * @return bool
	 */
	public static function isBlocked($mac)
	{
		// If the guest's mac is blocked in the estate, return true else false
		if(self::where('mac', $mac)->whereIn('site', session('admin.site.estate'))->first())
			return true;

		return false;
	}

	/**
	 * Getting a list of all blocked macs in a list of sites ex:estate
	 * @param $sites
	 * @return bool
	 */
	public static function getBlockedMacsBySites($sites)
	{
		return self::whereIn('site', $sites)->get()->keyBy('mac');

	}
}