<?php
namespace App\Models\AirConnect;
use App\Models\BaseModel;

class Country extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'country';
}