<?php
namespace App\Models\AirConnect;
use App\Models\BaseModel;

class Walledgarden extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'walledgarden';

	/**
	 * walledgarden belongs to site
	 * @return mixed
	 */
	public function walledgardenSite()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'id' );
	}
}