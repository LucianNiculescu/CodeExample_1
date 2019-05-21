<?php

namespace App\Admin\Helpers\Datatables;

use App\Helpers\DateTime;

/**
 * Helper Class
 * Class DurationColumn
 * @package App\Admin\Helpers\Datatables
 */
class HiddenColumn
{
	/**
	 * To convert time columns into human readable
	 * @param $data
	 * @return string
	 */
	public static function renderData($data)
	{
		return '<span style="display: none">'.$data.'</span>' ;
	}
}