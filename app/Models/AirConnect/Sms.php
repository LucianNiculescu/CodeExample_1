<?php

namespace App\Models\AirConnect;

use App\Models\BaseModel;


class Sms extends BaseModel
{
	protected $connection = 'airconnect';
    public $table = 'sms';
}
