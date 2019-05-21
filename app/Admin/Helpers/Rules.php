<?php
/**
 * Created by PhpStorm.
 * User: sheri
 * Date: 6/10/2016
 * Time: 10:34 AM
 */

namespace App\Admin\Helpers;

/**
 * Class Rules is the central location to store all rules needed in the form validation
 * TODO: Make and organize all rules the system needs
 * @package App\Admin
 */
class Rules
{
	// A list of contants will be arranged depending on modules or type
	const MAC 	        = 'required|min:17|regex:/^([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})$/';
	const REQUIRED_NAME = 'required|min:3|max:32';
	const NAME          = 'min:3|max:32';
	const REQUIRED 	    = 'required';
	const CSV 	        = 'required|mimes:csv,txt,html';
	// Images should be resized to 1920 by 1920 and maximum size is 3 Migs
	const JPG 	        = 'mimes:jpeg,jpg|max:3000';
	const PNG 	        = 'mimes:png|max:3000';


}