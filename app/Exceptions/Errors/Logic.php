<?php

namespace App\Exceptions\Errors;

use App\Portal\Logic as PortalLogic;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\Debug\Exception\FlattenException;

class Logic {
	public static $excludedUrlErrors = ['404', '401', '500', '503', 'TokenMismatch'];

	/**
	 * Generate data for 404 page
	 * @return array
	 */
	public static function get404ErrorData() {

		$data = self::getDefaultData(trans('admin.missing-page'), trans('admin.missing-page-description'), trans('admin.errors|wrong-page'));

		return $data;
	}

	/**
	 * Generate data for 401 page
	 * @return array
	 */
	public static function get401ErrorData() {

		$data = self::getDefaultData(trans('admin.unauthorized'), trans('admin.unauthorized-description'), trans('admin.errors|unauthorized'));

		return $data;
	}

	/**
	 * Generate data for 500 page
	 * @return array
	 */
	public static function get500ErrorData() {

		$data = self::getDefaultData(trans('admin.error'), trans('admin.something-wrong'), trans('admin.whoops-something-wrong'));

		return $data;
	}

	/**
	 * Generate data for 503 page
	 * @return array
	 */
	public static function get503ErrorData() {

		$data = self::getDefaultData(trans('admin.error'), trans('admin.something-wrong'), trans('error.hardware-found'));

		return $data;
	}

	/**
	 * Generate data for TokenMismatch
	 * NOT USED ANYMORE!!!
	 * @return array|mixed
	 */
	public static function getTokenMismatchErrorData() {

		$data = self::getDefaultData(trans('admin.timeout'), trans('portal.error-timeout'), trans('portal.error-timeout'));
		return $data;
	}

	/**
	 * Explode the url and gets the last element. Returns it if it's not one of the errors, else returns false
	 * @return array|bool|mixed
	 */
	private static function getLastUrl() {
		//check if there is an url for the 'previous page"
		if(session()->has('_previous')) {

			//get the last page that the user has been to (excluding 404,401 page)
			$last = explode('/', session('_previous')['url']);
			$last = end($last);

			if(!in_array($last, self::$excludedUrlErrors))
				return $last;
		}
		return false;
	}

	/**
	 * Returns basic data used for blades
	 * @param $title
	 * @param $description
	 * @param $error_message
	 * @param bool $logged_in
	 * @param bool $portal
	 * @return array
	 */
	private static function getDefaultData($title, $description, $error_message, $logged_in = false, $portal = false) {
		$data = [];
		$data['title']			= $title;
		$data['description'] 	= $description;
		$data['error_message'] 	= $error_message;
		$data['logged_in'] 		= $logged_in;
		$data['portal']			= $portal;
		$data['prev']			= '';
		$data['hideCreate']		= true;

		// Token mismatch on the portal, usually a time out of the token
		if(session()->has('admin') && !empty(auth()->user())) {
			$data = self::setKeyData($data, 'logged_in', true);
			//check if there is an url for the 'previous page"
			$last = self::getLastUrl();
			if($last)
				$data = self::setKeyData($data, 'prev', session('_previous')['url']);

		} elseif(session()->has('portal')) { //check if the user is logged in
			$data = self::setKeyData($data, ['portal', 'error_message'], [true, $error_message]);

		}

		return $data;
	}

	/**
	 * Set a key with the given value
	 * @param $data array
	 * @param $key string|array
	 * @param $value string|array
	 * @return array|bool
	 */
	private static function setKeyData($data, $key, $value) {
		if(!empty($data)) {
			if(is_array($key))
				foreach($key as $k => $v)
					$data[$v] = $value[$k];
			else
				$data[$key] = $value;

			return $data;
		}
		return false;
	}

	/**
	 * Returns the view without redirect so we can show the custom error message (of the exception if the debug is true)
	 * @param $exception
	 * @param $errorCode int id of the error code that we are logging with
	 * @return mixed
	 */
	public static function getDefaultError($exception, $errorCode) {
		$message = trans('admin.whoops-something-wrong');
		if(config('app.debug') && !empty($exception->getMessage()))
			$message = $exception->getMessage();

		//Get the random code that will act as an ID of the error (so it can be searched on the Logfile)
		$messageInfo = ' - Status: '.$exception->getStatusCode().' Error code: '.$errorCode; // TODO: Translate??
		$message .= $messageInfo;

		//get default data
		$data = self::getDefaultData(trans('admin.error'), trans('admin.something-wrong'), $message);

		if(session()->has('admin')) {
			//check if the user is logged in
			if (!empty(auth()->user()))
				$data = self::setKeyData($data, 'logged_in', true);

			//if the user is logged in return a view, else redirect to login page with an error message
			if ($data['logged_in'])
				return view("errors.default", $data);
			else
				return redirect()->to('/admin')->withErrors($data['error_message']);
		}

		//If it comes from a portal
		if($data['portal'])
			// TODO: Remove this as it will error, redirect and exit
			PortalLogic::errorPage('401', $data['error_message']);

		return response($data['error_message']);
	}

	/*
	 * Log and Slack an error, returning the random code
	 * We need to build a message for Slack and a message for the log.
	 * We use a randomly generated code as a reference for both and return that.
	 * @param $exception
	 * @return int $randomCode
	 */
	public static function reportError($exception) {

		$randomCode = rand(); //Generate a random code that will act as an ID of the error (so it can be searched on the Logfile)

		// If it's not actually an exception, just return our random.
		if (!$exception instanceof \Exception)
			return $randomCode;

		$message='';

		// If we are not getting a message we should try and get some more info:
		if (empty($exception->getMessage())) {

			// If it's a 404 error we know we don't get a message, but let's report the page requested.
			if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {

				// If we have a referer, show it - otherwise just the page requested.
				$message = !empty(request()->server('HTTP_REFERER'))
					? "Not Found: `".request()->server('REQUEST_URI')."` from `".request()->server('HTTP_REFERER')."`;"
					: "Not Found: `".request()->server('REQUEST_URI')."`;";

			} else {

				/* Otherwise it's an unknown exception with no message. Report:
						- The exception type
						- What page was being requested when it was thrown
						- The stack trace (in a Markdown ``` block)
					*/
				$message = "Unknown Exception! "
					. "Type: `".get_class($exception)
					. "`"
					. " at: `".request()->server('REQUEST_URI')
					. "`\n"
					. "Trace:\n```".$exception->getTraceAsString()
					. "```";
			}

		} else {

			// Alternatively, if we do have a message, just show it.
			$message = '"'.$exception->getMessage().'"';
		}

		// If the status is 418 do not report/log it
		$e = FlattenException::create($exception);
		$statusCode = $e->getStatusCode();
		if (in_array($statusCode, [418]))
			return '';

		$messageInfo = ' Error code: '.$randomCode; // TODO: Translate??

		//Set the server IP that's generating the error
		$message .= ' Server IP: '.request()->server('SERVER_ADDR'). '; ';

		// Set the user/guest IP that is generating the error (if possible). If it's behind a proxy, it will show the proxy server
		if(!empty(request()->ip()))
			$message .= 'User IP: '.request()->ip().'; ';

		//Put them all together
		$message .= $messageInfo;

		//Log the error with the actual message, even if the user just sees a generic message
		\Log::info($message);

		// Should we send a message to Slack?
		if(config('slack.enabled'))
        {
        	// Send message to Slack only if the error is not TokenMismatchException
			if (!$exception instanceof TokenMismatchException) {

				// We need the papertrail ID for this system's logging.
				// That is a system config, not part of the app, so we have to store it in the slack config
				// and rely on it being kept in sync with the system we're running on. (Dev/Staging/Production)
				$papertrailId = config('slack.papertrailId');

				// Build a URL to the message on papertrail, so we can use it in a hyperlink
				$papertrailLink = $papertrailId . "/events?q=" . $randomCode;
				$messageURL = "https://papertrailapp.com/groups/" . $papertrailLink;

				$attachmentForSlack = [
						"title"			=> $randomCode . ':' . $message,
						"title_link"	=> $messageURL,
						"fallback"		=> $papertrailLink,
						"color"			=> "warning"
					];

				// Prepare the message for transmission to Slack
				$slackMessage = new Slack();
				$slackMessage->attach($attachmentForSlack);

				$slackDriver = (new \Illuminate\Notifications\ChannelManager(app()))->channel('slack');
				$slackDriver->send($slackMessage, $slackMessage);
			}
        }

		return $randomCode;
	}
}