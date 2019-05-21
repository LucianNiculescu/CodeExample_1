<?php

namespace App\Admin\Helpers\Datatables;

use App\Models\AirConnect\Gateway as GatewayModel;
use App\Models\AirHealth\Hardware as HardwareModel;
use Carbon\Carbon;

/**
 * Put N/A Latency if packetloss is 100
 * Class GatewayLatencyColumn
 * @package App\Admin\Helpers\Datatables
 */
class GatewayLatencyColumn
{
	/**
	 * Takes the data and the row to check on the updated time and decide which icon to show beside the type
	 * @param $data
	 * @param $packetloss
	 * @return string
	 */
	public static function renderData($data, $packetloss = 0)
	{
		if($packetloss == 100)
			return trans('admin.n-a');

		return $data;
	}
}