<?php
namespace App\Models\AirConnect;
use App\Models\BaseModel;

class AdminAttribute extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'admin_attribute';
	public $timestamps = false;
	/**
	 * admin_attribute belongs to admin
	 * @return mixed
	 */
	public function adminAttributeAdmin()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Admin', 'id' );
	}
}