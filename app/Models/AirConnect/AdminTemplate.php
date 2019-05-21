<?php
namespace App\Models\AirConnect;
use App\Models\BaseModel;

class AdminTemplate extends BaseModel
{
	protected $connection = 'airconnect';
	protected $fillable = [ 'name', 'http', 'url', 'status' ];

	/**
	 * Admin Template has many Admin users
	 */
	public function admin()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Admin', 'id', 'template_id');
	}

}