<?php
namespace App\Models\AirConnect;
use App\Models\BaseModel;

class Browser extends BaseModel
{
	protected $connection = 'airconnect';

	public $timestamps = false;
	/**
	 * browsers belongs to site
	 * @return mixed
	 */
	public function browsersSite()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'id' );
	}
}