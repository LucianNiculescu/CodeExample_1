<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class PortalVisitors extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'portal_visitors';
    public $fillable = ['portal_id','guest_mac'];

	/**
	 * Do a join on the portal table, foreign key is id.
	 * portal_view can have many portal
	 * @return mixed
	 */
	public function portals()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Portal', 'portal_id', 'id' );
	}

	/**
	 * @return mixed
	 */
	public function guests()
	{
		return $this->belongsTo( '\App\Models\AirConnect\User', 'guest_mac', 'mac' );
	}
}