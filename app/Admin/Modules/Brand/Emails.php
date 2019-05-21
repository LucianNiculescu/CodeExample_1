<?php

namespace App\Admin\Modules\Brand;

use \App\Models\AirConnect\Content as ContentModel;
use App\Admin\Modules\Brand\Logic as Brand;
use \App\Admin\Helpers\Messages;
use \App\Helpers\Language;

class Emails
{
	/**
	 * Gets the email template contents from the DB
	 * @param string $portalId
	 * @param string $portalLanguage
	 * @param string $emailTemplateName
	 * @return mixed
	 */
	public static function getContents($portalId = '', $portalLanguage = '', $emailTemplateName = '')
	{
		$contents = ContentModel::with('portal')
			->whereIn('name', ['email_welcome', 'email_receipt'])
			->where('type', 'email');

		if ($portalId != '')
			$contents = $contents->where('portal', $portalId)
				->where('language', 'like' , $portalLanguage.'%')
				->where('type', 'email')
				->where('name', $emailTemplateName);

		$contents = $contents->get()->toArray();

		return $contents;
	}

	/**
	 * Preparing the edit view for the email template
	 * @param $portalId
	 * @param $emailTemplateName
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public static function editEmailTemplate($portalId, $emailTemplateName, $view)
	{
		// Setup the form's action and url
		$actionUrl = route('manage.brand.emails.edit');// '/manage/brand/saveEmailTemplate';
		$hiddenMethod = 'POST';

		$portals = Brand::getPortalsWithLanguage();

		if(!in_array($portalId, array_keys($portals)))
			abort('401', trans('error.not-authorized'));

		$portalLanguage = (isset($portal['attributes'][0]))? $portal['attributes'][0]['value'] : 'en'  ;

		if(!in_array($portalLanguage, Language::getLanguages()))
			$portalLanguage = 'en';

		// Getting the contents from the DB
		$contents = Emails::getContents($portalId, $portalLanguage, $emailTemplateName);

		\App::setlocale($portalLanguage);

		if(empty($contents))
			$emailContents = trans('content.' . $emailTemplateName);
		else
			$emailContents = $contents[0]['value'];

		\App::setlocale(session('admin.user.language'));

		if($view)
			$hideSave = 'hideSave';
		else
			$hideSave = '';

		// Data to be sent to the email template edit page
		$data =
			[
				'title' 			=> trans('admin.'.$portalLanguage) . ' - ' . trans('admin.'.$emailTemplateName) ,
				'description' 		=> $portals[$portalId]['name'] ,
				'emailContents'		=> $emailContents ,
				'portalLanguage'	=> $portalLanguage ,
				'portalId'			=> $portalId ,
				'emailTemplateName'	=> $emailTemplateName ,
				'hiddenMethod' 		=> $hiddenMethod ,
				'actionUrl' 		=> $actionUrl,
				'cancelUrl'			=> route('manage.brand.index'),
				'view'				=> $view,
				$hideSave			=> true,
			];

		// Show the edit form and pass the data
		return view('admin.modules.brand.edit-emails', $data);
	}

	/**
	 * Saves the email template in the content DB after deleting the existing one
	 * @return mixed
	 */
	public static function save()
	{
		$requestData = \Request::all();

		$portalId = $requestData['portal'];
		$portalLanguage = $requestData['language'];
		$emailTemplateName = $requestData['name'];

		$contents = self::getFirstContent($portalId , $portalLanguage , $emailTemplateName );

		if(!is_null($contents))
			$contents->delete();

		$model = new contentModel;
		$model::create($requestData);

		Messages::create(Messages::SUCCESS_MSG, trans('admin.email-template-saved'));

		return \Redirect::to( route('manage.brand.index') );
	}


	/**
	 * Gets the first content for the given portal
	 * @param $portalId
	 * @param $portalLanguage
	 * @param $emailTemplateName
	 * @return mixed
	 */
	public static function getFirstContent($portalId , $portalLanguage , $emailTemplateName )
	{
		return ContentModel::where('portal', $portalId)
			->where('language', 'like' , $portalLanguage.'%')
			->where('name', $emailTemplateName)
			->where('type', 'email')
			->first();
	}
}