<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Vendor extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'vendor';
	public $timestamps = false;
}