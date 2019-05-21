<?php

namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Vlan extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'vlan';
	protected $fillable = ['location_id', 'vlan'];

	/**
	 * VLAN belongs to Location
	 * @return mixed
	 */
	public function location()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Location' );
	}
}
