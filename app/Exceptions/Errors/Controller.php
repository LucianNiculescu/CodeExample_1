<?php
namespace App\Exceptions\Errors;

use Illuminate\Routing\Controller as BaseController;
use App\Exceptions\Errors\Logic as ErrorLogic;
use App\Portal\Logic as PortalLogic;


/**
 * Class Controller
 * Handles the errors
 */
class Controller extends BaseController
{
	/**
	 * Display 404 based on the logged in user
	 * @param $code int the code of the error (ex: 404, 401...)
	 * @param $errorCode int id of the error code that we are logging with
	 * @return mixed /redirect to error blade or login if the user is not logged in
	 */
	public function index($code, $errorCode) {
		$method = "get{$code}ErrorData";
		$data = ErrorLogic::$method();

		//Generate a random code that will act as an ID of the error (so it can be searched on the Logfile)
		$messageInfo = ' Error code: '.$errorCode; // TODO: Translate??
		$data['error_message'] .= $messageInfo;

		//if the user is logged in return a view, else redirect to login page with an error message
		if(session()->has('admin')) {
			if($data['logged_in'])
				return view("errors.default", $data);
			else
				return redirect()->to('/')->withErrors($data['error_message']);
		}

		//If it comes from a portal
		if($data['portal'])
			// TODO: Remove this as it will error, redirect and exit
			PortalLogic::errorPage('401', $data['error_message']);

		return false;
	}
}