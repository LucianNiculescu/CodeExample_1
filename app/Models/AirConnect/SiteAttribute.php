<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class SiteAttribute extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'site_attribute';
	public $fillable = ['ids','name','value', 'type','status'];

	/**
	 * site_attribute belongs to site
	 * @return mixed
	 */
	public function siteAttribute()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'id' );
	}
}