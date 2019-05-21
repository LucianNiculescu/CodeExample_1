<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class PackageAttribute extends BaseModel
{
	protected $connection = 'airconnect';
	/**
	 * The table associated with the model
	 *
	 * @var string
	 */
	public $table = 'package_attribute';

	/**
	 * The attributes which represent timestamps
	 *
	 * @var array
	 */
	public $timestamps = ['created', 'updated'];

	/**
	 * The attributes that are mass assignable
	 *
	 * @var array
	 */
	protected $fillable = [
		'ids',
		'name',
		'type',
		'value',
		'status'
	];

	/**
	 * The name of the created_at field
	 *
	 * @var string
	 */
	const CREATED_AT = 'created';

	/**
	 * The name of the updated_at field
	 *
	 * @var string
	 */
	const UPDATED_AT = 'updated';

	/**
	 * package_attribute belongs to package
	 * @return mixed
	 */
	public function package()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Package', 'id' );
	}
}