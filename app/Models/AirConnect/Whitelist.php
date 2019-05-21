<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Whitelist extends BaseModel
{
	protected $connection = 'airconnect';
	/**
	 * The table associated with the model
	 *
	 * @var string
	 */
	protected $table = 'whitelist';

	/**
	 * The attributes that are mass assignable
	 *
	 * @var array
	 */
	protected $fillable = [
		'site',
		'mac',
		'description',
		'performedby'
	];

	/**
	 * The attributes which represent timestamps
	 *
	 * @var array
	 */
	public $timestamps = ['created', 'updated'];

	/**
	 * The name of the created_at field
	 *
	 * @var string
	 */
	const CREATED_AT = 'created';

	/**
	 * Model does not have updated_at field
	 *
	 * @var string
	 */
	const UPDATED_AT = 'updated';

	/**
	 * Get the Admin who created the Whitelist record
	 *
	 * @return mixed
	 */
	public function admin()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Admin', 'performedby', 'username' );
	}

	/**
	 * Get the Site where the Whitelist record belongs
	 *
	 * @return mixed
	 */
	public function parentSite()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'site' );
	}
}