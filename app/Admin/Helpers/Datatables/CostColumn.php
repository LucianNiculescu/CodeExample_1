<?php

namespace App\Admin\Helpers\Datatables;

use App\Helpers\CurrencyHelper;

/**
 *
 * Class CostColumn
 * @package App\Admin\Helpers\Datatables
 */
class CostColumn
{
	/**
	 * Converting add currency symbol to the cost or type free if it is 0.00
	 * @param $data
	 * @return string|\Symfony\Component\Translation\TranslatorInterface
	 */
	public static function renderData($data)
	{
		if($data == '0.00')
			return trans('admin.free');
		else
			return CurrencyHelper::getCurrencySymbol() .$data ;
	}
}