<?php
namespace App\Models\AirConnect;
use App\Models\BaseModel;

class Content extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'content';
	public $fillable = ['site','portal','name','type','value','language','status'];

	/**
	 * Do a join on the content_attribute table, foreign key is ids.
	 * content can have many content_attribute
	 * @return mixed
	 */
	public function contentAttributes()
	{
		return $this->hasMany( '\App\Models\AirConnect\ContentAttribute', 'ids' );
	}

	/**
	 * content belongs to portal
	 * @return mixed
	 */
	public function portal()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Portal', 'portal', 'id' );
	}

	/**
	 * content belongs to site
	 * @return mixed
	 */
	public function content_site()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'site', 'id' );
	}
}