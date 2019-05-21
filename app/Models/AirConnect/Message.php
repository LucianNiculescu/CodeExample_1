<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Message extends BaseModel
{
	protected $connection = 'airconnect';
	public $fillable = ['site','type','description','user_id','role_id'];
	public $timestamps = false;

	/**
	 * user_id is the foreign key of Admin
	 * id is the primary key of Admin
	 *
	 * messages belongs to admin
	 * @return mixed
	 */
	public function admin()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Admin', 'user_id', 'id'  );
	}

	/**
	 * messages belongs to site
	 * @return mixed
	 */
	public function messagesSite()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site' );
	}
}