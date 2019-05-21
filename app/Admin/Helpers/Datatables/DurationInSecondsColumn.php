<?php

namespace App\Admin\Helpers\Datatables;

use App\Helpers\DateTime;

/**
 * Helper Class
 * Class DurationInSecondsColumn to convert the data in seconds
 * @package App\Admin\Helpers\Datatables
 */
class DurationInSecondsColumn
{
	/**
	 * To convert time columns into human readable
	 * @param $data
	 * @return string
	 */
	public static function renderData($data)
	{
		if(!is_numeric($data))
			$data = DateTime::convertTextTimeToSeconds($data);

		return $data;
	}
}