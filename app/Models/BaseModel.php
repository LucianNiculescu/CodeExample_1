<?php


/**
 * Class BaseModel
 * We need to change the created_at and updated_at table names as we are using create and updated
 */
namespace App\Models;
use App\Admin\Helpers\ModelObserver;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
	const CREATED_AT = 'created';
	const UPDATED_AT = 'updated';
}