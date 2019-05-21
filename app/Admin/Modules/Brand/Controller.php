<?php
namespace App\Admin\Modules\Brand;

use App\Admin\Modules\Brand\Logic as Brand;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
	/**
	 * Will redirect to index page
	 * @param $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function show($id)
	{
		return \Redirect::to( route('manage.brand.index') );
	}

	/**
	 * Show the form for viewing the specified Email Template.
	 * @param $portalId
	 * @param $emailTemplateName
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function viewEmailTemplate($portalId, $emailTemplateName)
	{
		return Emails::editEmailTemplate($portalId, $emailTemplateName, true);
	}

	/**
	 * Show the form for editing the specified Email Template.
	 * @param $portalId
	 * @param $emailTemplateName
	 * @param bool $view
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @internal param $voucherType
	 */
	public function editEmailTemplate($portalId, $emailTemplateName, $view = false)
	{
		return Emails::editEmailTemplate($portalId, $emailTemplateName, $view);
	}

	/**
	 * Show the form for viewing the specified voucher.
	 * @param $siteId
	 * @param $lang
	 * @param $voucherType
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @internal param $portalId
	 * @internal param $emailTemplateName
	 */
	public function viewVoucher($siteId, $lang, $voucherType)
	{
		return Vouchers::editVoucher($lang, $voucherType, true);
	}

	/**
	 * Show the form for editing the specified voucher.
	 * @param $siteId
	 * @param $lang
	 * @param $voucherType
	 * @param bool $view
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @internal param $portalId
	 */
	public function editVoucher($siteId, $lang, $voucherType, $view = false)
	{
		return Vouchers::editVoucher($lang, $voucherType, $view);
	}

	/**
	 * Show the form for editing the specified Terms and conditions .
	 * @param $portalId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function viewTerms($portalId)
	{
		return Terms::editTerms($portalId, true);
	}

	/**
	 * Show the form for editing the specified Terms and conditions .
	 * @param $portalId
	 * @param bool $view
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function editTerms($portalId, $view = false)
	{
		return Terms::editTerms($portalId, $view );
	}

	/**
	 * Show the form for editing the specified Terms and conditions .
	 * @param $language
	 * @param $siteId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function viewSiteTerms($language, $siteId)
	{
		return Terms::siteTerms($language, $siteId, true); // Read only
	}

	/**
	 * Show the form for editing the specified Terms and conditions.
	 * @param $language
	 * @param $siteId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function editSiteTerms($siteId, $language )
	{
		return Terms::siteTerms($siteId, $language); // With edit
	}

	/**
	 * Saves Terms and conditions
	 * @return mixed
	 */
	public function siteTerms( )
	{
		// If we want to delete
		$request = \Request::all();
		if( isset($request['action']) && $request['action'] == 'delete' )
			return Brand::delete();

		// If we want to save
		return Terms::siteSave();
	}

	/**
	 * Saves Terms and conditions
	 * @return mixed
	 */
	public function terms( )
	{
		// If we want to delete
		$request = \Request::all();
		if( isset($request['action']) && $request['action'] == 'delete' )
			return Brand::delete();

		// If we want to save
		return Terms::save();
	}

	/**
	 * Saves email template
	 * @return mixed
	 */
	public function emails()
	{
		// If we want to delete
		$request = \Request::all();
		if( isset($request['action']) && $request['action'] == 'delete' )
			return Brand::delete();

		return Emails::save();
	}

	/**
	 * Save or delete voucher
	 * @return mixed
	 */
	public function voucher()
	{
		// If we want to delete
		$request = \Request::all();
		if( isset($request['action']) && $request['action'] == 'delete' )
			return Brand::delete();

		return Vouchers::save();
	}


	/**
	 * deletes the content from the DB
	 * @return int
	 */
	public function deleteContent()
	{
		return Brand::delete();
	}

	/**
	 * Save the look and feel of a brand
	 */
	public function saveLookAndFeel($siteId)
	{
		return LookAndFeel::save($siteId);
	}

	/**
	 * Display a listing of the Brand
	 */
	public function index()
	{
		// Getting the contents from the DB
		$contents = Emails::getContents();

		// Getting the terms and conditions
		$terms = Terms::getTerms();

		// Getting the Vouchers
		$vouchers = Vouchers::getVouchers();

		// Getting a list of portals
		$portals = Brand::getPortalsWithLanguage();

		// Setting up data for the view
		$lookAndFeelData = LookAndFeel::setupView();

		// Terms & Conditions for this site for each language
		$siteLanguageTerms = Terms::getSiteLanguageTerms(); // ['en' => null];

		$data = [
			'title' 		=> trans('admin.brand-title'),
			'description'	=> trans('admin.brand-desc'),
			'lookAndFeel'	=> $lookAndFeelData ,
			'contents'		=> $contents , // i.e. email templates
			'terms'			=> $terms ,
			'vouchers'		=> $vouchers ,
			'portals'		=> $portals ,
			'siteId'		=> session('admin.site.loggedin'),
			'hideCreate'	=> true,
			'siteLanguageTerms' 	=> $siteLanguageTerms
		];

		return view('admin.modules.brand.index', $data);
	}
}