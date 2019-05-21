<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Note extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'notes';
	public $fillable = ['note', 'type', 'user_id' , 'site_id', 'status', 'expires'];

	/**
	 * package belongs to site
	 * @return mixed
	 */
	public function site()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'id' );
	}

	/**
	 * package belongs to user
	 * @return mixed
	 */
	public function user()
	{
		return $this->belongsTo( '\App\Models\AirConnect\User', 'id' );
	}

	/**
	 * Return the types of the notes having the indexes 'Error' | 'Warning' | 'Hardware'
	 * @return array
	 */
	public static function getTypes() {
		return ['Error' => trans('admin.error'), 'Warning' => trans('admin.warning'), 'Hardware' => trans('admin.hardware')];
	}

}