<?php

namespace App\Admin\Helpers\Datatables;

/**
 * Helper Class
 * Class ExplodeToLabelsColumn
 * @package App\Admin\Helpers\Datatables
 */
class ExplodeToLabelsColumn
{
	/**
	 * Shows related columns as labels in the datatable
	 * @param $data
	 * @return string
	 */
	public static function renderData($data)
	{
		$exploaded = explode(',', $data);
		$returnData = '';

		foreach($exploaded as $item)
		{
			$item = trans('admin.' . $item);
			$returnData .= '<div><span class="label label-default margin-right-5" title="">' . $item . '</span></div>';
		}


		return $returnData;
	}
}