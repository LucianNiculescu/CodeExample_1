<?php
namespace App\Models\AirConnect;
use App\Models\BaseModel;

class Bypassed extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'bypassed';
	public $timestamps = false;

	/**
	 * bypassed belongs to site
	 * @return mixed
	 */
	public function bypassedSite()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'id' );
	}
}