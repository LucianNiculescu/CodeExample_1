<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Location extends BaseModel
{
	protected $connection = 'airconnect';
	protected $fillable = array('site_id', 'name', 'room_no', 'type', 'status', 'name');

	/**
	 * location belongs to site
	 * @return mixed
	 */
	public function site()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site' );
	}

	/**
	 * location belongs to many portals
	 * @return mixed
	 */
	public function portals()
	{
		return $this->belongsToMany( '\App\Models\AirConnect\Portal' );
	}

	/**
	 * Do a join on the Vlan table, foreign key is location_id.
	 * location can have many vlan
	 * @return mixed
	 */
	public function vlan()
	{
		return $this->hasMany( '\App\Models\AirConnect\Vlan' );
	}
}