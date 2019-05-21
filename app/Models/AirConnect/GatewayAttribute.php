<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class GatewayAttribute extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'gateway_attribute';
	public $fillable = ['ids','name','value', 'type','status'];

	/**
	 * site_attribute belongs to site
	 * @return mixed
	 */
	public function gateAttribute()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Gateway', 'id' );
	}
}