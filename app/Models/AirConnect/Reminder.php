<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Reminder extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'reminder';

	/**
	 * reminder belongs to site
	 * @return mixed
	 */
	public function reminderSite()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'id' );
	}

}