<?php
namespace App\Models\AirConnect;
use App\Models\BaseModel;

class ContentAttribute extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'content_attribute';

	/**
	 * content_attribute belongs to content
	 * @return mixed
	 */
	public function contentAttributeContent()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Content', 'id' );
	}
}