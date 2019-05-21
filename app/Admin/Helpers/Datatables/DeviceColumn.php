<?php

namespace App\Admin\Helpers\Datatables;

use App\Admin\Modules\Guests\Logic as Guests;

/**
 * Helper class
 * Class DeviceColumn
 * @package App\Admin\Helpers\Datatables
 */
class DeviceColumn
{
	/**
	 * Display the vendor beside the mac
	 * @param $data
	 * @return string
	 */
	public static function renderData($data)
	{
		return $data . '<div class="mac-vendor">(' . Guests::getVendorFromMac($data) .')</div>';
	}
}