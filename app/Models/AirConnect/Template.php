<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Template extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'template';

	/**
	 * template belongs to site
	 * @return mixed
	 */
	public function templateSite()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'id' );
	}
}