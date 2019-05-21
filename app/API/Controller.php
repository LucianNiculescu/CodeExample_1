<?php

namespace App\API;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class Controller
 * API controller will pass to the correct version
 * @package App\API
 */
class Controller extends BaseController
{
	/**
	 * @param int $version 			1 only
	 * @param string $type 			Model name to use
	 * @param string $id 			ID of the model to use
	 * @param bool | String $join 	CSV of Models or 1 Model to join to
	 * @return string 				Json encoded string
	 */
	public function index($version, $type, $id, $join=false)
	{
		// Create the class
		$class = "App\\API\\v$version\\Logic";

		// Create the obj
		$obj = new $class;

		// Run the correct method
		return $obj->get($type, $id, $join);
	}

}