<?php

namespace App\Admin\Helpers\Datatables;

/**
 * Helper class
 * Class CommaSeparatedColumn
 * @package App\Admin\Helpers\Datatables
 */
class CommaSeparatedColumn
{
	/**
	 * Display the vendor beside the mac
	 * @param $data
	 * @return string
	 */
	public static function renderData($data)
	{
		return str_replace(",","<br>",$data);
	}
}