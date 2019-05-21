<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Portal extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'portal';
    public $fillable = ['site','name','status'];

	/**
	 * Do a join on the content table, foreign key is portal.
	 * portal can have many content
	 * @return mixed
	 */
	public function contents()
	{
		return $this->hasMany( '\App\Models\AirConnect\Content', 'portal' );
	}

	/**
	 * Do a join on the portal_attribute table, foreign key is ids.
	 * portal can have many portal_attribute
	 * @return mixed
	 */
	public function attributes()
	{
		return $this->hasMany( '\App\Models\AirConnect\PortalAttribute', 'ids' );
	}

	/**
	 * @return mixed
	 */
	public function forms()
	{
		return $this->belongsToMany( '\App\Models\AirConnect\Form' );
	}

	/**
	 * @return mixed
	 */
	public function activeForms()
	{
		return $this->belongsToMany( '\App\Models\AirConnect\Form' )->where('status', 'active');
	}

	/**
	 * @return mixed
	 */
	public function visitors()
	{
		return $this->hasMany( '\App\Models\AirConnect\PortalVisitors' , 'portal_id');
	}

	/**
	 * Do a join on the locations table, foreign key is portal.
	 * portal belongs to many locations
	 * @return mixed
	 */
	public function locations()
	{
		return $this->belongsToMany( '\App\Models\AirConnect\Location' );
	}

	/**
	 * portal belongs to site
	 * @return mixed
	 */
	public function site()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'site' );
	}

	/**
	 * Returns a valid path to an asset specified in $filename. Returns false if file
	 * does not exist by when appended to public_path()
	 *
	 * @param  $filename
	 * @return bool|string
	 */
	public function getUploadPath($filename)
	{
		$path = '/uploads/' . $this->id  . '/' . $filename;

		return file_exists(public_path() . $path) ? $path : false;
	}
}