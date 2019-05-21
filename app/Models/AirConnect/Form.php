<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Form extends BaseModel
{
	protected $connection = 'airconnect';
    public $fillable = ['name' ,'status', 'site_id'];

	/**
	 * Form has many questions
	 * @return mixed
	 */
	public function questions()
	{
		return $this->belongsToMany( '\App\Models\AirConnect\Question' );
	}

	/**
	 * Form
	 * @return mixed
	*/
	public function portals()
	{
		return $this->belongsToMany( '\App\Models\AirConnect\Portal' );
	}


	/**
	 * form belongs to site
	 * @return mixed
	 */
	public function site()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site' );
	}

	/**
	 * form has many excluded packages
	 * @return mixed
	 */
	public function excludedPackages()
	{
		return $this->hasMany( '\App\Models\AirConnect\FormExcludedPackages' );
	}
}