<?php

namespace App\Admin\Modules\Brand;

use \App\Models\AirConnect\Content as ContentModel;
use \App\Admin\Helpers\Messages;

class Vouchers
{
	/**
	 * Getting terms contents
	 * @param string $lang
	 * @param string $voucherType
	 * @return mixed
	 */
	public static function getVouchers($lang = '', $voucherType = '')
	{
		$terms = ContentModel::with('portal')
			->where('type', 'content')
			->where('site', session('admin.site.loggedin'))
			->orderBy('id', 'DESC');

		if($lang != '')
			$terms = $terms->where('language', $lang)
				->where('name', $voucherType);
		else
			$terms = $terms->whereIn('name', ['single_voucher', 'multiple_voucher']);


		$terms = $terms->get()->toArray();

		return $terms;
	}

	/**
	 * Preparing the edit view for the terms section
	 * @param $lang
	 * @param $voucherType
	 * @param $view
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public static function editVoucher($lang, $voucherType, $view)
	{
		// Getting the contents from the DB
		$vouchers = self::getVouchers($lang, $voucherType);

		\App::setlocale($lang);

		if(empty($vouchers))
			$contents = trans('content.' . $voucherType);
		else
			$contents = $vouchers[0]['value'];

		\App::setlocale(session('admin.user.language'));

		if($view)
			$hideSave = 'hideSave';
		else
			$hideSave = '';

		// Data to be sent to the email template edit page
		$data =
			[
				'title' 			=> trans('admin.'.$lang) . ' - ' . trans('admin.'.$voucherType) ,
				'description' 		=> '' ,
				'contents'		=> $contents ,
				'language'		=> $lang ,
				'portalId'		=> 0 ,
				'voucherType'	=> $voucherType,
				'siteId'		=> session('admin.site.loggedin'),
				'hiddenMethod' 	=> 'POST' ,
				'actionUrl' 	=> '/manage/brand/vouchers/edit',
				'cancelUrl'		=> route('manage.brand.index'),
				'view'				=> $view,
				$hideSave			=> true,
			];

		// Show the edit form and pass the data
		return view('admin.modules.brand.edit-voucher', $data);
	}

	/**
	 * Saving terms after deleting existing ones
	 * @return mixed
	 */
	public static function save()
	{
		$requestData = \Request::all();

		$lang = $requestData['language'];
		$voucherType = $requestData['name'];

		$vouchers = self::getVouchers($lang , $voucherType );

		if(!is_null($vouchers))
		{
			foreach($vouchers as $voucher)
			{
				$model = new contentModel;
				$tempTerm = $model::find($voucher['id']);
				$tempTerm->delete();
			}
		}

		$model = new contentModel;
		$model::create($requestData);

		Messages::create(Messages::SUCCESS_MSG, trans('admin.voucher-saved'));

		return \Redirect::to( route('manage.brand.index') );
	}
}