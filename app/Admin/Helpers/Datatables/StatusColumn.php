<?php

namespace App\Admin\Helpers\Datatables;


/**
 * Helper Class
 * Class StatusColumn
 * Special class to show the substable status
 * It will take the authorized data and bypassed data and also blocked
 * @package App\Admin\Helpers\Datatables
 */
class StatusColumn
{
	/**
	 * @param $row
	 * @param $blockedList
	 * @return string
	 */
	public static function renderData($row, $blockedList)
	{
		if(in_array($row['mac'], array_keys($blockedList)))
		{
			$statusTitle = trans('admin.blocked');
			$statusClass = 'label-danger';
		}
		elseif(isset($row['authorized']) and $row['authorized'] === 'true')
		{
			$statusTitle = trans('admin.authorized');
			$statusClass = 'label-success';
		}
		elseif(isset($row['bypassed']) and $row['bypassed'] === 'true')
		{
			$statusTitle = trans('admin.bypassed');
			$statusClass = 'label-success';
		}
		elseif(isset($row['session_id']))
		{
			$statusTitle = 'radius';
			$statusClass = 'label-info';
		}else
		{
			$statusTitle = trans('admin.pending');
			$statusClass = 'label-warning';
		}

		// Returning an icon and a hidden div to take care of the sorting
		//return '<i title = "'. $statusTitle .'" class="fa fa-circle  '. $statusClass .'"></i><div class="hide">' . $statusTitle . '</div>';
		return '<span class="label '. $statusClass .' margin-right-5">' . $statusTitle . '</span>';

	}

}