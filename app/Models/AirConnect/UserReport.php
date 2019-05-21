<?php

namespace App\Models\AirConnect;

use App\Models\BaseModel;

class UserReport extends BaseModel
{
	protected $connection = 'airconnect';
    public $table = 'user_report';
    public $timestamps = false;
}
