<?php

namespace App\Models\AirConnect;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 * @package App\Models\AirConnect
 * TODO: Categories table has only id and category, need to check the relationship
 */
class Category extends Model
{
	protected $connection = 'airconnect';
}
