<?php

namespace App\Admin\Helpers\Datatables;

use App\Helpers\DateTime;

/**
 * Helper Class
 * Class DurationColumn
 * @package App\Admin\Helpers\Datatables
 */
class DurationColumn
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

		// putting a hiddin span infront of the time to be able to sort it
		return '<span style="display: none">'.$data.'</span>' . DateTime::seconds2readable($data);
	}
}