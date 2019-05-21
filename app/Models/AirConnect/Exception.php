<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Exception extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'exception';
	public $timestamps = false;
}