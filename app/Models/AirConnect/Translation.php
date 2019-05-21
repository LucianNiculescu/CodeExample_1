<?php
namespace App\Models\AirConnect;
use App\Helpers\Language;
use App\Models\BaseModel;
/**
 * Class Translation
 * @package App\Models\AirConnect
 * TODO: To create a translation table under airconnect, currently it is in simplifi_db
 */
class Translation extends BaseModel
{
	protected $connection = 'airconnect';
	// Primary Key for
	protected $primaryKey = 'key';

	protected $fillable = [ 'key', 'type', 'en', 'fr', 'es' ,'de' , 'ar', 'tr' ];

	// We do not have timestamps on this table so we disable them
	public $timestamps = false;

	// Primary Key isn't an incrementing integer , This will fix the toArray issue to convert the key to 0
	public $incrementing = false;

	/**
	 * Get the languages
	 * Create and translate the languages
	 */
	public static function getLanguages($type = null)
	{
		$langList = Language::getLanguages($type);

		$languages = [];

		foreach ($langList as $lang )
			$languages[$lang] = trans('admin.' .$lang);

		return $languages;
	}
}