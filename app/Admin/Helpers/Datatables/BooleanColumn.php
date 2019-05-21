<?php

namespace App\Admin\Helpers\Datatables;

/**
 * Helper class to render the bool columns
 * Class BooleanColumn
 * @package App\Admin\Helpers\Datatables
 */
class BooleanColumn
{
	/**
	 * render the bool columns with icons: Tick for Yes, Cross for No
	 * @param $data
	 * @return string
	 */
	public static function renderData($data)
	{
		return $data == 0 ? '<i class="fa fa-times"></i>' : '<i class="fa fa-check"></i>';
	}
}