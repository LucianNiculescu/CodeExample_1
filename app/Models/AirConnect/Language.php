<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;
/**
 * Class Translation
 * @package App\Models\AirConnect
  */
class Language extends BaseModel
{
	protected $connection = 'airconnect';
	// Primary Key for
	protected $primaryKey = 'key';

	protected $fillable = [ 'key', 'name', 'admin', 'portal' ];

	// We do not have timestamps on this table so we disable them
	public $timestamps = false;

	// Primary Key isn't an incrementing integer , This will fix the toArray issue to convert the key to 0
	public $incrementing = false;

}