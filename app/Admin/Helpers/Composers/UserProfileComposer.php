<?php

namespace App\Admin\Helpers\Composers;
use App\Models\AirConnect\Translation;

class UserProfileComposer
{

	public function compose($view)
	{
		$view->with('languages', $this->getLanguageInfo());
		$view->with('timezones', $this->getTimezoneInfo());
	}

	public function getLanguageInfo()
	{
		return Translation::getLanguages('admin');
	}

	public function getTimezoneInfo()
	{
		// Create the languages
		return $timezones = [
			'Europe/Lisbon' => trans( 'admin.europe_lisbon' ),
			'Europe/London' => trans( 'admin.europe_london' ),

		];
	}
}