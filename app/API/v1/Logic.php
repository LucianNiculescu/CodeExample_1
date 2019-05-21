<?php
namespace App\API\v1;

use App\Models\AirConnect\Package;

/**
 * Class Logic
 * API V1
 * Each function must refer to a Model to return
 * @package App\API
 */
class Logic
{
	/**
	 * Packages
	 * @param string $type
	 * @param int $id ID of the model
	 * @param bool | string $join CSV of joins or nothing
	 * @return object
	 */
	public function get(string $type, int $id, $join=false)
	{
		$model = self::stringToModel($type);

		if( $join !== false )
			return $model::where('id',$id)->with(explode(',',$join))->first();
		else
			return $model::find($id);
	}


	/**
	 * @param $string
	 * @return mixed|string
	 */
	public static function stringToModel($string)
	{
		$db = 'App\\Models\\AirConnect\\';
		$string = ucwords(strtolower($string));

		foreach (array('-', '\'') as $delimiter) {
			if (strpos($string, $delimiter)!==false) {
				$string =implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
			}
		}

		$string = str_replace('-', '', $string);

		$string = $db .$string;
		return $string;
	}
}