<?php

namespace App\Admin\Helpers\Datatables;

use App\Helpers\DateTime;

/**
 * Helper class to render the date columns
 * Class DateColumn
 * @package App\Admin\Helpers\Datatables
 */
class DateColumn
{
	/**
	 * render the date columns to medium date
	 * @param $data
	 * @return string
	 */
	public static function renderData($data)
	{
		return DateTime::medium($data, true);
	}
}