<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Logging extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'logging';
	public $timestamps = false;
}