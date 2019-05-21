<?php

namespace App\Admin\Helpers\Datatables;

use App\Models\AirConnect\Gateway as GatewayModel;

/**
 * Class helper
 * Class GatewayTypeColumn
 * @package App\Admin\Helpers\Datatables
 */
class GatewayTypeColumn
{
	/**
	 * To Display the gateway type from the key
	 * @param $data
	 * @return mixed
	 */
	public static function renderData($data)
	{
		return GatewayModel::getGatewayTypes()[$data];
	}
}