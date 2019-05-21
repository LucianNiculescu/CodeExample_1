<?php

namespace App\Models\AirConnect;

use App\Models\BaseModel;

class SsidSchedule extends BaseModel
{
	protected $connection = 'airconnect';
    public $table = "ssid_schedule";
	public $timestamps = false;

}
