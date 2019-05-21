<?php

namespace App\Models\AirConnect;

use App\Models\BaseModel;

class SsidWritable extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = "ssid_writable";
}
