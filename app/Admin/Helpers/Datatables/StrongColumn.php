<?php

namespace App\Admin\Helpers\Datatables;

use App\Helpers\DateTime;

/**
 * Helper Class
 * Class StrongColumn
 * @package App\Admin\Helpers\Datatables
 */
class StrongColumn
{
	/**
	 * To convert time columns into human readable
	 * @param $data
	 * @return string
	 */
	public static function renderData($data)
	{

		return '<strong>'.$data.'</strong>';
	}
}