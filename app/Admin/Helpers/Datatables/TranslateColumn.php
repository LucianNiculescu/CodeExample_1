<?php

namespace App\Admin\Helpers\Datatables;

/**
 * Helper class
 * Class TranslateColumn
 * @package App\Admin\Helpers\Datatables
 */
class TranslateColumn
{
	/**
	 * Translates the data
	 * @param $data
	 * @return string|\Symfony\Component\Translation\TranslatorInterface
	 */
	public static function renderData($data)
	{
		return trans('admin.' . $data);
	}
}