<?php

namespace App\Admin\Helpers\Datatables;

use App\Helpers\DateTime;

/**
 * Helper class to render the date columns
  * Class DateNoTimeColumn
 * @package App\Admin\Helpers\Datatables
 */
class DateNoTimeColumn
{
	/**
	 * Same as DateColumn Class but not showing the time
	 * @param $data
	 * @return string
	 */
	public static function renderData($data)
	{
		return DateTime::medium($data);
	}
}