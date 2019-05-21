<?php
/**
 * Created by PhpStorm.
 * User: sheri
 * Date: 24-Feb-17
 * Time: 4:29 PM
 */

namespace App\Helpers;

use App\Models\AirConnect\Admin as AdminModel;

class UrlHelper
{
	/*
	 * Generate the url based on userId (getting it from 'admin_templates' table)
	 * @return url String or false
	 */
	public static function getUrl($userId) {
		$admin = AdminModel::find($userId)->adminTemplate()->first();
		if(!empty($admin->url)) {
			if($admin->http == 1)
				$url = 'http://'.$admin->url.'/';
			else
				$url = 'https://'.$admin->url.'/';
			return $url;
		}

		// Return the default URL if there is no admin user URL
		return config('app.url').'/';
	}

	/**
	 * Split Url
	 * Split the url passed in into protocol and path (e.g 'https://' and 'google.com')
	 * @param $url
	 * @return array
	 */
	public static function splitUrl( $url ) {
		if( strpos( $url, 'https://' ) === false ) {
			$redirectURL = str_replace( "http://", "", $url );
			$secureRedirect = 'false';
		} else {
			$redirectURL = str_replace( "https://", "", $url );
			$secureRedirect = 'true';
		}

		return [
			'protocol'	=> $secureRedirect,
			'url'		=> $redirectURL
		];
	}

	/**
	 * This function is used as a curl wrapper
	 * At the moment we use GuzzleHttp
	 * The reason we have this is to be able to use/change it from one place
	 * @param $url
	 * @param string $method
	 * @param string $action
	 * @param array $parameters
	 * @param array $auth
	 * @param bool $ssl_verify
	 * @param bool $wget 			Use a wget, not guzzle
	 * @return string
	 */
	public static function callClient($url, $method='GET', $action='', $parameters=[], $auth=[], $ssl_verify=true, $wget=false)
	{
		if($wget){
			return file_get_contents("$url?" .http_build_query($parameters));
		}else{
			// Added verify = false to allow self-signed certs. Remove this for production
			$client = new \GuzzleHttp\Client(['base_uri' => $url, 'verify' => $ssl_verify] + $auth);

			try {
				$request = $client->request($method, $action, ['query' => $parameters]);
			} catch (\Exception $e) {
				\Log::info($e->getMessage()); // Log the actual error in case it's useful later
				abort('501', 'Curl Error');
			}

			return $request->getBody()->getContents();
		}

	}

	/**
	 * Downloads a page based on a given URL
	 * @param $url
	 * @return mixed
	 */
	public static function downloadPage($url) {
		$rd=rand(1,10000);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url."?".$rd);
		curl_setopt($ch, CURLOPT_FAILONERROR,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		$retValue = curl_exec($ch);
		curl_close($ch);
		return $retValue;
	}
}