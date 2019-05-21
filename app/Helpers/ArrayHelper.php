<?php

namespace app\Helpers;


class ArrayHelper
{
	/**
	 * This translates array values if needed
	 * @param $array
	 * @return mixed
	 */
	public static function translateArray($array)
	{
		foreach($array as &$item)
		{
			$item = trans('admin.'.$item);
		}

		return $array;
	}
}