<?php

namespace App\Admin\Helpers;

/**
 * Helper class for social media functions
 * Class SocialMedia
 * @package App\Admin\Helpers
 */
class SocialMedia
{
	const urlArray = [
		'facebook'	=> 'https://www.facebook.com/',
		'twitter'	=> 'https://www.twitter.com/',
		'google'	=> 'https://plus.google.com/',
		'linkedin'	=> 'https://www.linkedin.com/in/',
		'live'		=> 'https://account.microsoft.com/account/',
	];


	/**
	 * Getting the Profile Url from the id
	 * @param $website
	 * @param $id
	 * @return mixed
	 */
	public static function getProfileUrl($website, $id)
	{
		return self::urlArray[$website] . $id;
	}
}