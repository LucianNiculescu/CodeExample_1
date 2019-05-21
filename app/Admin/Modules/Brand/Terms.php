<?php

namespace App\Admin\Modules\Brand;

use \App\Models\AirConnect\Content as ContentModel;
use \App\Admin\Helpers\Messages;
use \App\Helpers\Language;
use App\Models\AirConnect\Site;
use App\Models\AirConnect\Translation;

class Terms
{
	/**
	 * Get the Terms for this site and add them to the language
	 * @param null $siteId
	 * @return array
	 */
	public static function getSiteLanguageTerms( $siteId=null )
	{
		// Check we have a site or use the session
		if(is_null($siteId))
			$siteId = session('admin.site.loggedin');

		// Fill the array with the languages
		$siteLanguageTerms = [];

		// Get the Terms for the site
		$terms = ContentModel::where([
			'site' 		=> $siteId,
			'name' 		=> 'terms',
			'status' 	=> 'active'
		])
			->get()
			->keyBy('language');

		// Fix the language
		if( isset($terms['en_GB']) )
		{
			$terms['en'] = $terms['en_GB'];
			unset($terms['en_GB']);
		}

		// Loop through the languages
		foreach (Language::getLanguages() as $lang )
		{
			// If we have terms for this language
			if( isset($terms[$lang]) )
			{
				$siteLanguageTerms[$lang]['id'] = $terms[$lang]->id;
				$siteLanguageTerms[$lang]['value'] = $terms[$lang]->value;
			}else
				$siteLanguageTerms[$lang] = null;
		}
		return $siteLanguageTerms;
	}


	/**
	 * @param $language
	 * @param null $siteId
	 * @param bool $readOnly
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public static function siteTerms($siteId=null, $language, $readOnly=false)
	{
		// Get the Terms with the site
		$contents = self::getSiteTerms($language, $siteId);

		// Data to be sent to the view
		$data = [
			'title' 			=> trans('admin.'.$language) .' - ' .trans('admin.terms'),
			'description' 		=> '',
			'contents'			=> is_null($contents) ? '' : $contents,
			'portalLanguage'	=> $language,
			'portalId'			=> null,
			'hiddenMethod' 		=> 'POST',
			'actionUrl' 		=> route('manage.brand.site.terms.edit'), //'/manage/brand/site-terms/edit',
			'cancelUrl'			=> route('manage.brand.index'),
			'view'				=> $readOnly
		];

		// If we want read only we should hide the save button
		if($readOnly)
			$data['hideSave'] = true;

		// Show the edit form and pass the data
		return view('admin.modules.brand.edit-terms', $data);
	}


	/**
	 * Get the Terms for this site in this language
	 * If we have no terms for this site, inherit.
	 * Still no terms, use the default from the translation
	 * @param int|null $siteId
	 * @param null $language
	 * @return mixed
	 */
	private static function getSiteTerms( $language=null, $siteId=null )
	{
		$contents = null;

		// If we have a language and a site
		if( !is_null($siteId) && !is_null($language) )

			// Loop until we have contents or we hit site 0
			while ($contents == null && $siteId != 0)
			{
				// Get terms for this site and language
				$contents = Site::where([
					'id' => $siteId
				])
				->with(['content' => function($query) use ($language){
					$query->where([
						'name' 		=> 'terms',
						'language' 	=> $language,
						'status' 	=> 'active'
					])->first();
				}])
					->first();

				// Set the site to check as the parent of the last site
				$siteId = $contents->parent;

				// If we have contents then set it, else set as null and try again
				$contents = $contents->content->isEmpty() ? null : $contents->content[0]->value;
			}

		// If we have no content
		if( is_null($contents) )
			// Get the default terms for this language
			$contents = Translation::where([
				'type' 		=> 'content',
				'key' 		=> 'terms'
			])
				->first()
				->$language;

		return $contents;
	}


	/**
	 * Getting terms contents
	 * @param string $portalId
	 * @param string $portalLanguage
	 * @return mixed
	 */
	public static function getTerms($portalId = '', $portalLanguage = '')
	{
		$terms = ContentModel::with('portal')
			->where('name', 'terms')
			->where('type', 'content')
			->orderBy('id', 'DESC');

		if ($portalId != '')
			$terms = $terms->where('portal', $portalId)
				->where('language', 'like' , $portalLanguage.'%');

		$terms = $terms->get()->toArray();

		return $terms;
	}


	/**
	 * Preparing the edit view for the terms section
	 * @param $portalId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public static function editTerms($portalId, $view)
	{
		// Setup the form's action and url
		$actionUrl = route('manage.brand.terms.edit');
		$hiddenMethod = 'POST';

		$portals = Logic::getPortalsWithLanguage();

		if(!in_array($portalId, array_keys($portals)))
			abort('401', trans('error.not-authorized'));

		$portalLanguage = (isset($portal['attributes'][0]))? $portal['attributes'][0]['value'] : 'en'  ;

		if(!in_array($portalLanguage, Language::getLanguages()))
			$portalLanguage = 'en';

		// Getting the contents from the DB
		$terms = Terms::getTerms($portalId, $portalLanguage);

		\App::setlocale($portalLanguage);

		if(empty($terms))
			$contents = trans('content.terms');
		else
			$contents = $terms[0]['value'];

		\App::setlocale(session('admin.user.language'));

		if($view)
			$hideSave = 'hideSave';
		else
			$hideSave = '';
		// Data to be sent to the email template edit page
		$data =
			[
				'title' 		=> trans('admin.'.$portalLanguage) . ' - ' . trans('admin.terms') ,
				'description' 	=> $portals[$portalId]['name'] ,
				'contents'		=> $contents ,
				'portalLanguage'=> $portalLanguage ,
				'portalId'		=> $portalId ,
				'hiddenMethod' 	=> $hiddenMethod ,
				'actionUrl' 	=> $actionUrl,
				'cancelUrl'		=> route('manage.brand.index'),
				'view'			=> $view,
				$hideSave		=> true,
			];

		// Show the edit form and pass the data
		return view('admin.modules.brand.edit-terms', $data);
	}


	/**
	 * Saving terms for a site
	 * @return mixed
	 */
	public static function siteSave()
	{
		// Get the data we need to add to the DB
		$requestData = \Request::all();

		// Get the data or create an object
		$terms = ContentModel::firstOrNew([
			'site' 		=> session('admin.site.loggedin'),
			'name' 		=> $requestData['name'],
			'language' 	=> $requestData['language'],
			'type' 		=> 'content',
		]);

		// Update the obj and save it
		$terms->value = $requestData['value'];
		$terms->status = 'active';
		$terms->save();

		// Tell the user and redirect
		Messages::create(Messages::SUCCESS_MSG, trans('admin.terms-saved'));
		return \Redirect::to( route('manage.brand.index') );
	}


	/**
	 * Saving terms after deleting existing ones
	 * @return mixed
	 */
	public static function save()
	{
		// Set up the data
		$requestData = \Request::all();
		$portalId = $requestData['portal'];
		$portalLanguage = $requestData['language'];

		// V1 may have many sets of Terms because of a bug, this is how we handle
		$terms = self::getTerms($portalId , $portalLanguage);
		if(!is_null($terms))
		{
			foreach($terms as $term)
			{
				$model = new contentModel;
				$tempTerm = $model::find($term['id']);
				$tempTerm->delete();
			}
		}

		$model = new contentModel;
		$model::create($requestData);

		// Tell the user and redirect
		Messages::create(Messages::SUCCESS_MSG, trans('admin.terms-saved'));
		return \Redirect::to( route('manage.brand.index') );
	}
}