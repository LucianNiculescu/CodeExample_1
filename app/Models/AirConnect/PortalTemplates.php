<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class PortalTemplates extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'portal_templates';
	protected $fillable = [ 'title', 'description', 'status', 'created', 'updated' ];
	public $timestamps = true;

}