<?php

namespace App\Admin\Helpers\Datatables;

/**
 * Helper Class
 * Class RelatedColumn
 * @package App\Admin\Helpers\Datatables
 */
class RelatedColumn
{
	/**
	 * Shows related columns as labels in the datatable
	 * @param $data
	 * @return string
	 */
	public static function renderData($data)
	{
		$relationArray = json_decode($data, TRUE);
		$returnData = '';

		foreach($relationArray as $relation)
		{
			// If relationship with site it should show the site name
			if(isset($relation['site']))
				$title = $relation['site']['name'];
			else
				$title = '';

			$returnData .= '<span class="label label-default margin-right-5" title="'. $title .'">' . $relation['name'] . '</span>';
		}


		return $returnData;
	}
}