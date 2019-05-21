<?php
namespace App\Helpers;

class FileHelper
{
	/**
	 * Take in bytes and format to the correct type
	 * @param $bytes
	 * @param int $precision
	 * @param string $class
	 * @return string
	 */
	public static function bytesToReadable($bytes, $precision = 2, $class = '')
	{
		$closeTag = $openTag = '';
		if(!empty($class))
		{
			$openTag = '<span class="'.$class.'">';
			$closeTag = '</span>';
		}


		if ($bytes >= 1099511627776)
			$bytes = round($bytes / 1099511627776, $precision) . $openTag . ' TB' . $closeTag;

		elseif ($bytes >= 1073741824)
			$bytes = round($bytes / 1073741824, $precision) . $openTag . ' GB' . $closeTag;

		elseif ($bytes >= 1048576)
			$bytes = round($bytes / 1048576, $precision) . $openTag . ' MB' . $closeTag;

		elseif ($bytes >= 1024)
			$bytes = round($bytes / 1024, $precision) . $openTag . ' kB' . $closeTag;

		elseif ($bytes > 1)
			$bytes = round($bytes, $precision) . $openTag . ' bytes' . $closeTag;

		elseif ($bytes == 1)
			$bytes = $bytes . $openTag . ' byte' . $closeTag;

		else
			$bytes = '0' . $openTag . 'bytes' . $closeTag;

		return $bytes;
	}


	/**
	 * Take in megabytes and format to the correct type
	 * @param $megabytes
	 * @param int $precision
	 * @return string
	 */
	public static function megabytesToReadable($megabytes, $precision = 2)
	{
		return self::bytesToReadable($megabytes*1024, $precision);
	}
}