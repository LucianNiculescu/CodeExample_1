<?php
namespace App\Admin\Modules\Help;

use App\Models\AirConnect\Translation;

class Logic
{
	// Multidimensional array holding all the help for this language
	private static $helpArray = [];


	/**
	 * Set the multidimensional array to hold the system help for this language
	 */
	public static function setHelpArray()
	{
		// Get the language
		$language = session('admin.user.language');

		// Get all the help from the translation DB in our language
		$help = self::getLanguageHelp($language);

		// Set the the collection key
		$help = $help->keyBy('key');

		// Loop through the array
		foreach ($help as $key => $item) {
			// Explode the key
			$explodedKey = explode('|', $key);

			// Create the multidimensional array
			if( isset($explodedKey[0]) && isset($explodedKey[1]) && isset($explodedKey[2]) && isset($explodedKey[3]) )
				// Set the value into the multidimensional array
				self::$helpArray[ $explodedKey[0] ][ $explodedKey[1] ][ $explodedKey[2] ][ $explodedKey[3] ] = $item->value;
			elseif( isset($explodedKey[0]) && isset($explodedKey[1]) && isset($explodedKey[2]) )
				// Set the value into the multidimensional array
				self::$helpArray[ $explodedKey[0] ][ $explodedKey[1] ][ $explodedKey[2] ] = $item->value;
			elseif( isset($explodedKey[0]) && isset($explodedKey[1]) )
				// Set the value into the multidimensional array
				self::$helpArray[ $explodedKey[0] ][ $explodedKey[1] ] = $item->value;
			elseif( isset($explodedKey[0]) )
				// Set the value into the multidimensional array
				self::$helpArray[ $explodedKey[0] ]['index'] = $item->value;
		}
	}



	/**
	 * @return array
	 */
	public static function getHelpArray()
	{
		return self::$helpArray;
	}



	/**
	 * Get the help for this language
	 * @param null $lang
	 * @return mixed
	 */
	private static function getLanguageHelp($lang=null)
	{
		// If the language is null, use the default
		if($lang == null)
			$lang = config('app.locale');

		return Translation::where([
			'type'=>'help',
		])
			->select('key', $lang .' AS value')
			->get();
	}
}