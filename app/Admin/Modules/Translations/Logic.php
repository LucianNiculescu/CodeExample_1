<?php
namespace App\Admin\Modules\Translations;

use \App\Admin\Helpers\Messages;
use App\Models\AirConnect\Translation as TranslationModel;
use App\Admin\Helpers\Rules;
use App\Jobs\UploadTranslationJob;
use App\Helpers\Language;

class Logic
{
	/**
	 * Uploads the CSV to the database into the Translation table
	 * @param null $id
	 * @param null $status
	 * @return $this|\Illuminate\Http\RedirectResponse|int
	 */
	public static function saveForm($id = null, $status = null)
	{
		$url = '/translations';

		// HTML rules
		$rules =
			[
				'translation-csv'	=>	Rules::CSV,
			];

		// collects all data from the form and do validation
		$requestData = \Request::all();
		$validator = \Validator::make($requestData, $rules);

		// if validation fails it returns back to the same page and refill all fields except password
		if ($validator->fails())
		{
			return \Redirect::to($url)
				->withErrors($validator)->withInput();
		}
		else
		{
			$filename = 'translation_'.mt_rand().'.csv';
			$destination = public_path().'/uploads/';
			$file = \Input::file('translation-csv')->move($destination, $filename);
			if($file) {
				dispatch(new UploadTranslationJob($destination.$filename, 'App\Models\AirConnect\Translation' , false, session('admin.user.username')));
				Messages::create('success', trans('admin.uploaded'));
				return \Redirect::to($url);
			}
			return false;
		}
	}

	/**
	 * Get Translation
	 * Pass in a 2 digit language code and output the cached version of that language
	 * @param $lang
	 * @return array|mixed
	 */
	public static function getTranslationCache( $lang , $type)
	{

		$expires = 2; // Days the translation will expire

		// Check if the array is in the cache
		if ( \Cache::has( $type . '.translation.' .$lang ) )
		{
			// Use the cache
			$translations = \Cache::get( $type . '.translation.' .$lang );
		}
		else
		{
			// Create the translations array
			$translations = [];

			$allTrans = TranslationModel::select( 'key', config('app.locale'), $lang )
				->where('type',$type)
				->get();
			// Go through all the translations for this lang
			foreach( $allTrans as $trans )
			{
				// Set the translation
				if( $trans->$lang != null )
					$translations[$trans->key] = $trans->$lang;
				else
					$translations[$trans->key] = $trans->{config('app.locale')};
			}

			// Add translation to the cache
			$expiresAt = \Carbon\Carbon::now()->addDays( $expires );
			\Cache::put(  $type . '.translation.' . $lang , $translations, $expiresAt );
		}

		return $translations;
	}

	/**
	 * Checks the Translation Existence and insert it to the datatabse if it is not there with
	 * @param $id
	 */
	public static function insertTranslation($id)
	{
		$noTranslationFound = '--not translated--';

		$transkey = explode('.', $id);

		$key 	= $transkey[1];
		$type 	= $transkey[0];
		$lang = Language::getLanguage(true);

		$cachedTranslations = self::getTranslationCache( $lang , $type);

		// Checking if translation is not in cache, then check the database
		if(!isset($cachedTranslations[$key]))
			$translation = TranslationModel::where(['key' => $key, 'type' => $type])->first();
		else
			$translation = $cachedTranslations[$key];

		// If no translation found then insert it to the database
		if(is_null($translation))
			TranslationModel::insert(['key' => $key, 'type' => $type, 'en' => $noTranslationFound .  $key]);
/* Commenting out the english check for now
 		elseif(empty($translation->en) or is_null($translation->en))
			TranslationModel::where(['key' => $key, 'type' => $type])
				->update(['en' => $noTranslationFound .  $key]);
*/

	}
}