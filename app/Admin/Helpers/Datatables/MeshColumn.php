<?php

namespace App\Admin\Helpers\Datatables;

use App\Helpers\DateTime;

/**
 * Helper Class
 * Class DurationColumn
 * @package App\Admin\Helpers\Datatables
 */
class MeshColumn
{
	/**
	 * To convert mesh if it is disabled / 0
	 * @param $data
	 * @return string
	 */
	public static function renderData($data)
	{
		if($data == 'Disabled / 0')
			$data = trans('admin.wired');

		return $data;
	}
}