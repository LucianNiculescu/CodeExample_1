<?php

namespace App\Helpers;

use App\Models\AirConnect\PortalAttribute as PortalAttributeModel;
use App\Models\AirConnect\SiteAttribute as SiteAttributeModel;

class CurrencyHelper{

	/*public static $currencies = [
		'GBP' => '£',
		'USD' => '$',
		'SAR' => '﷼',
		'EUR' => '€',
	];*/

	public static $currencies = [
		'AED' => [
			'title' 	=> 'United Arab Emirates dirham',
			//'symbol' 	=> 'AED',
			'symbol' 	=> '&#x62f;&#x2e;&#x625;',
		],
		'AUD' => [
			'title' 	=> 'Australian dollar',
			'symbol' 	=> 'AUD$',
		],
		'BHD' => [
			'title' 	=> 'Bahraini dinar',
			'symbol' 	=> '.د.ب',
		],
		'CAD' => [
			'title' 	=> 'Canadian dollar',
			'symbol' 	=> 'C$',
		],
		'CHF' => [
			'title' 	=> 'Swiss franc',
			'symbol' 	=> 'CHF',
		],
		'CNY' => [
			'title' 	=> 'Chinese yuan',
			'symbol' 	=> '¥',
		],
		'DJF' => [
			'title' 	=> 'Djiboutian Franc',
			'symbol' 	=> 'Fdj',
		],
		'EGP' => [
			'title' 	=> 'Egyptian pound',
			'symbol' 	=> 'ج.م',
		],
		'EUR' => [
			'title' 	=> 'Euro',
			'symbol' 	=> '&euro;',
		],
		'GBP' => [
			'title' 	=> 'British pound',
			'symbol' 	=> '£',
		],
		'GHS' => [
			'title' 	=> 'Ghanaian cedi',
			'symbol' 	=> 'GH¢',
		],
		'IDR' => [
			'title' 	=> 'Indonesian rupiah',
			'symbol' 	=> 'Rp',
		],
		'INR' => [
			'title' 	=> 'Indian rupee',
			'symbol' 	=> '₹',
		],
		'JOD' => [
			'title' 	=> 'Jordanian dinar',
			'symbol' 	=> 'د.ا',
		],
		'JPY' => [
			'title' 	=> 'Japanese yen',
			'symbol' 	=> '&yen;',
		],
		'LBP' => [
			'title' 	=> 'Lebanese pound',
			'symbol' 	=> 'ل.ل',
		],
		'LKR' => [
			'title' 	=> 'Sri Lankan rupee',
			'symbol' 	=> 'LKR',
		],
		'KWD' => [
			'title' 	=> 'Kuwaiti dinar',
			'symbol' 	=> 'د.ك',
		],
		'NZD' => [
			'title' 	=> 'New Zealand dollar',
			'symbol' 	=> 'NZ$',
		],
		'PHP' => [
			'title' 	=> 'Philippine peso',
			'symbol' 	=> '',
		],
		'PLN' => [
			'title' 	=> 'Polish zloty',
			'symbol' 	=> 'ZL',
		],
		'PKR' => [
			'title' 	=> 'Pakistani rupee',
			'symbol' 	=> '₨',
		],
		'QAR' => [
			'title' 	=> 'Qatari riyal',
			'symbol' 	=> 'ر.ق',
		],
		'SAR' => [
			'title' 	=> 'Saudi riyal',
			'symbol' 	=> '﷼',
		],
		'THB' => [
			'title' 	=> 'Thai baht',
			'symbol' 	=> '฿',
		],
		'TND' => [
			'title' 	=> 'Tunisian dinar',
			'symbol' 	=> 'د.ت',
		],
		'TRY' => [
			'title' 	=> 'Turkish lira',
			'symbol' 	=> 'TRY',
		],
		'USD' => [
			'title' 	=> 'United States dollar',
			'symbol' 	=> '$',
		],
		'VND' => [
			'title' 	=> 'Vietnamese dong',
			'symbol' 	=> '₫',
		],
	];


	/**
	 * Get all the currency symbols that exist
	 * @return array
	 */
	public static function getCurrencySymbols()
	{
		$symbols = array_column(self::$currencies, 'symbol');
		return $symbols;
	}


	/**
	 * Get all the currency symbols that exist as KEYs and 3 letter ISO code plus symbol as VALUE
	 * @return array
	 */
	public static function getCurrencySymbolsAndISO()
	{
		$symbols = [];
		foreach(self::$currencies as $key => $value){
			$symbols[$key] = $key .' (' .$value['symbol'] .')';
		}
		return $symbols;
	}

	/**
	 * Get the Currency Symbol
	 * To get the symbol we need to jump through some hoops
	 *  	1. Is there a Portal with the currency passed in
	 *  	2. Is there a Site with the currency passed in
	 *  	3. Is there a Portal ID in session, find the Portal and get the currency for it
	 * 		4. If there is a Portal Site ID in session, get the currency from the site
	 * 		5. If there is an admin logged in site, get the site then the currency from it
	 * 		6. Return the default
	 * @param null $portal
	 * @param null $site
	 * @return string
	 */
	public static function getCurrencySymbol($portal=null, $site=null)
	{
		$siteId = null; // Set the site ID
		$portalId = null; // Set the portal ID

		// If there is a Portal, check the currency
		if( !is_null($portal) )
		{
			// If we have attributes set the key correctly by the name
			if( isset($portal->attributes) )
				$portal->attributes = $portal->attributes->keyBy('name');

			// Check the attributes
			if( isset($portal->attributes->currency) && $portal->attributes->currency->value != 'none' )
				return self::getCurrencySymbolFromCode( $portal->attributes->currency->value );
		}

		// If there is a Site, check the currency
		if( !is_null($site) )
		{
			// If we have attributes set the key correctly by the name
			if( isset($site->attributes) )
				$site->attributes = $site->attributes->keyBy('name');

			// Check the attributes
			if( isset($site->attributes->currency) )
				return self::getCurrencySymbolFromCode( $site->attributes->currency->value );

			// If there is nothing in session
			$siteId = $site->id;
		}

		// If there is a portal.id in session, get the portal and attributes and check the currency
		if(session('portal.id'))
		{
			$portalId = session('portal.id');
			$currency = PortalAttributeModel::where([
				'ids' 	=> $portalId,
				'name' 	=> 'currency'
			])
				->first();

			if( !empty($currency ))
				return self::getCurrencySymbolFromCode( $currency->value );
		}

		// If there is a portal.site_id, get the site and attributes and check the currency
		if(is_null($site) && session('admin.site.loggedin'))
			$siteId = session('admin.site.loggedin');

		// If there is a admin.loggedin, get the site and attributes and check the currency
		if(is_null($site) && session('admin.loggedin'))
			$siteId = session('admin.loggedin');

		// If we have a site ID
		if(!is_null($siteId))
			$currency = SiteAttributeModel::where([
				'ids' 	=> $siteId,
				'name' 	=> 'currency'
			])
				->first();

		if( !empty($currency ))
			return self::getCurrencySymbolFromCode( $currency->value );

		// All else fails, use the default
		return config('app.currency_symbol');
	}


	/**
	 * Pass in the code, get the symbol
	 * @param null|string $currencyCode 	3 Diget ISO code, see https://developer.paypal.com/docs/classic/api/currency_codes/
	 * @return string 						Currency symbol related to the code
	 */
	public static function getCurrencySymbolFromCode($currencyCode=null)
	{
		// Return the default if null
		if( is_null( $currencyCode))
			return config('app.currency_symbol');

		if( isset( self::$currencies[$currencyCode] ))
			return self::$currencies[$currencyCode]['symbol'];

		// Return the default
		return config('app.currency_symbol');
	}
}