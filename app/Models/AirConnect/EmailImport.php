<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class EmailImport extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'email_import';
	public $timestamps = false;
}