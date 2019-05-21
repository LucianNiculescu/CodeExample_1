<?php

namespace App\Admin\Helpers;

use App\Helpers\DateTime;
use App\Helpers\FileHelper;
use App\Models\AirangelTools\Gender as GenderModel;


/**
 * Class HumanReadable: is responsible for transforming a value into seconds or MB based on a given type
 * @package App\Admin\Helpers
 */

class HumanReadable
{
	/**
	 * Transforms the value given to bytes/MB/time human readable
	 * @param $type
	 * @param $value
	 * @return string
	 */
	public static function readable($type, $value) {
		if(empty($type))
			return $value;

		//All the types that will be changed, values will be given in seconds
		$readSeconds = [
			'accounting-interval',
			'idle-timeout',
			'max-all-session',
			'max-daily-session',
			'timeout',
			'duration'

		];

		//All the types that will be changed, values will be given in bytes
		$readBytes = [
			'max-total',
		];

		//All the types that will be changed, values will be given in MegaBytes
		$readMegaBytes = [
			'candengo-daily-mb',
			'downstream',
			'upstream',
			'max-all-mb',
		];

		if(in_array(strtolower($type),$readBytes))
			$value = FileHelper::bytesToReadable($value);
		elseif(in_array(strtolower($type), $readMegaBytes))
			$value = FileHelper::megabytesToReadable($value);
		elseif(in_array(strtolower($type), $readSeconds))
			$value = DateTime::seconds2readable($value);

		return $value;
	}

	/**
	 * Checks the name with the names that we have in our DB to return the gender
	 * 0 - Female
	 * 1 - Male
	 * 2 - Unisex
	 * 3 - Default
	 * 4 - Name not found in our DB
	 *
	 * @param $forename
	 * @return int
	 */
	public static function getGender($forename) {
		$sex = GenderModel::select('sex')->where('name', ucfirst($forename))->get()->first();
		if (empty($sex))
			return 4;

		switch ($sex->sex) {
			case "Female":
				$gender = 0;
				break;

			case "Male":
				$gender = 1;
				break;

			case "Unisex":
				$gender = 2;
				break;

			default:
				$gender = 3;
				break;
		}

		return $gender;
	}
}