<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class PortalAttribute extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'portal_attribute';
    public $fillable = ['ids', 'name', 'value', 'type', 'status'];

	/**
	 * portal_attribute belongs to portal
	 * @return mixed
	 */
	public function portal()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Portal', 'ids' );
	}
}