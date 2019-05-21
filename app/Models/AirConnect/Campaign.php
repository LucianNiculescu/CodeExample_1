<?php

namespace App\Models\AirConnect;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

/**\
 * Class Campaign
 * @package App\Models\AirConnect
 * TODO: Check relationships and fillable
 */
class Campaign extends BaseModel
{
	protected $connection = 'airconnect';
}
