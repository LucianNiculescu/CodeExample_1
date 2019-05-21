<?php

namespace App\Admin\Helpers\Datatables;

use App\Helpers\FileHelper;

/**
 * Helper class to make byte columns readable
 * Class ByteColumn
 * @package App\Admin\Helpers\Datatables
 */
class ByteColumn
{
	/**
	 * Make $data readable and assigning a class to the scale 'i.e. MB'
	 * @param $data
	 * @return string
	 */
	public static function renderData($data)
	{
		return FileHelper::bytesToReadable($data, 2, 'datatable-byte-scale');
	}
}