<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class FormExcludedPackages extends BaseModel
{
	protected $connection = 'airconnect';
    public $fillable = ['form_id' ,'package_type'];
    public $timestamps = false;

	/**
	 * Belongs to 1 form
	 * @return mixed
	 */
	public function forms()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Form' );
	}
}