<?php

namespace App\Admin\Helpers\Datatables;

use Carbon\Carbon;

/**
 *
 * @package App\Admin\Helpers\Datatables
 */
class HheStatusColumn
{
	/**
	 * Takes the data and the row to check on the updated time and decide which icon to show beside the type
	 * @param $data
	 * @param $lastSeen
	 * @return string
	 */
	public static function renderData($data, $lastSeen = null)
	{
		$returnData = '<div class="text-center">';
		if($lastSeen < (Carbon::now()->subMinutes(15)))
		{
			$title = trans('admin.inactive-ap');
			$returnData .= '<img height="20" title="' . $title . '" width="20" src="/admin/templates/system/images/networkicons/wifi_off.png">&nbsp;';
		}
		else
		{
			$title = trans('admin.active-ap');
			$returnData .= '<img height="20" title="'.$title.'" width="20" src="/admin/templates/system/images/networkicons/wifi_on.png">&nbsp;';

		}
		$returnData .= '<span class="hide">'.$title.'</span>';
		$returnData .= '</div>';

		return $returnData;
	}
}