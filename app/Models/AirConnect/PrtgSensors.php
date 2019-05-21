<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class PrtgSensors extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'prtg_sensors';
	public $timestamps = false;
	protected $fillable = array('name', 'type', 'group', 'status', 'status_raw', 'site', 'sensor_id', 'parent_id', 'updated', 'created');

	/**
	 * transaction belongs to site
	 * @return mixed
	 */
	public function site()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'site' );
	}

}