<?php

namespace App\Admin\Helpers\Datatables;

use App\Models\AirConnect\Gateway as GatewayModel;
use App\Models\AirHealth\Hardware as HardwareModel;
use Carbon\Carbon;

/**
 * Class Helper to show the hardware type and icon
 * Class HardwareTypeColumn
 * @package App\Admin\Helpers\Datatables
 */
class HardwareTypeColumn
{
	/**
	 * Takes the data and the row to check on the updated time and decide which icon to show beside the type
	 * @param $data
	 * @param $updated
	 * @return string
	 */
	public static function renderData($data, $updated = null)
	{
		$allGatewayTypes = GatewayModel::getGatewayTypes();
		$allHardwareTypes = HardwareModel::getHardwareTypes();

		$title = $allHardwareTypes[$data] ?? $allGatewayTypes[$data] ?? '';

		if(in_array($data, array_keys($allGatewayTypes)))
			$fileName = 'gateway';
		else
			$fileName = (strtolower($data) == '') ? 'default' : strtolower($data);

		if(is_null($updated))
			return $allHardwareTypes[$data] ?? $allGatewayTypes[$data] ?? '';

		if($updated < (Carbon::now()->subHour()))
			$returnData = '<img height="20" title="'.$title.'" width="20" src="/admin/templates/system/images/networkicons/' . $fileName . '_off.png">&nbsp;';
		elseif($updated < (Carbon::now()->subMinutes(15)))
			$returnData = '<img height="20" title="'.$title.'" width="20" src="/admin/templates/system/images/networkicons/' . $fileName . '_warn.png">&nbsp;';
		else
			$returnData = '<img height="20" title="'.$title.'" width="20" src="/admin/templates/system/images/networkicons/' . $fileName . '_on.png">&nbsp;';

			$returnData .= '<span class="hide">'.$title.'</span>';

		return $returnData;
	}
}