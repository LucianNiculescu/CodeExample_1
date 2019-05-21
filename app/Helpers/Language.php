<?php

namespace App\Helpers;

use App\Models\AirConnect\Language as LanguageModel;
use Illuminate\Support\Facades\Schema;

class Language
{
	// Array of system languages - use trans('admin.' .$lang) for the string
	public static $langList = [
		'ar',
		'de',
		'en',
		'fr',
		'es',
		'tr'
	];

	/**
	 * Get the language from session and fall back to default if not in $langList array
	 * @param bool $admin 		Are we in Admin or Portal
	 * @return mixed|string
	 */
    public static function getLanguage($admin=false)
    {
    	// Portal session path
    	$sessionPath = 'portal.guest.language';

		// If we are admin side
		if($admin)
			// Use the admin session
			$sessionPath = 'admin.user.language';

		// Check the Portal session for a language
		if( session()->has($sessionPath))
			return session($sessionPath);

        // Get the first 2 chars of the browser lang
		$language = substr(\Request::server('HTTP_ACCEPT_LANGUAGE'), 0, 2);

        // If we are not 1 of the supported languages, use default
        if( in_array( $language, self::getLanguages() ) )
		{
			// Set the session language
			session(['portal.guest.language' => $language]);

			// Return the language
			return $language;
		}

		// Set the session language
        session([$sessionPath => config('app.locale')]);

		// Return the default language
        return config('app.locale'); // 'en';
    }

	/**
	 * @param $type : 'admin', 'portal' or null(default)
	 * @return array
	 */
	public static function getLanguages($type = null)
	{
		// If the languages table has not been created, return a static list so the migration can be done
		if (!Schema::connection('airconnect')->hasTable('languages'))
			return self::$langList;

		// If no type has been specified, get the language key for all languages and types
		$languages = LanguageModel::select('key');

		// If a type has been specified then filter for active languages for that type (active indicated by the value 1 in the $type field)
		if(!is_null($type))
			$languages = $languages->where($type, 1);

		// Get the (possibly filtered) languages as an array
		$languages = $languages->distinct('key')->get()->pluck('key')->toArray();

		// The English language is not stored in the languages table but is always included in the return languages
		$returnLanguages = array_merge(['en'], $languages);

		return $returnLanguages;
	}
}
