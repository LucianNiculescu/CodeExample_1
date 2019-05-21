<?php

namespace App\Admin\Helpers\Datatables;

/**
 * Helper Class
 * Class IconColumn
 * @package App\Admin\Helpers\Datatables
 */
class IconColumn
{
	/**
	 * Constant showing all guest types and their font awsome icon
	 */
	const typeIconList = [

		'airpass'				=>	'fa-envelope-o',
		'email'					=>	'fa-envelope-o',
		'csv'					=>	'fa-file-excel-o',
		'facebook'				=>	'fa-facebook',
		'gha'					=>	'fa-glide-g',
		'pms'					=>	'fa-hotel',
		'google'				=>	'fa-google',
		'linkedin'				=>	'fa-linkedin',
		'microsoft'				=>	'fa-windows',
		'live'					=>	'fa-windows',
		'paypal'				=>	'fa-paypal',
		'quick_login'			=>	'fa-flash',
		'twitter'				=>	'fa-twitter',
		'user-generated'		=>  'fa-user',
		'true'					=>  'fa-circle text-success',
		'false'					=>  'fa-stop text-danger',
		'whitelist'				=>  'fa-sign-in',
		'voucher'				=>  'fa-ticket',
		'voyat'					=>  'fa-vimeo',
		'voucher_guest_stub'	=>  'fa-tag'
	];

	/**
	 * Converts the type into an icon
	 * @param $data
	 * @return string
	 */
	public static function renderData($data)
	{
		if(in_array(strtolower($data), array_keys(self::typeIconList)))
			return '<i title = "'. trans('admin.'.$data) .'" class="fa '. self::typeIconList[strtolower($data)] .'"></i>';
		else
			return $data;
	}
}