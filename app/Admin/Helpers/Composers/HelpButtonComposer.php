<?php

namespace App\Admin\Helpers\Composers;

class HelpButtonComposer {

	/**
	 * Add Help page translation key to the view data
	 * @param $view \Illuminate\View\View 	'admin.help-pages.button'
	 */
	public function compose($view)
	{
		// If we have a view page with help, use it
		if( isset($view->helpPage) && $view->helpPage != '' ){
			$helpPage = $view->helpPage;

		// We do not have any help page so we should create it
		}else{
			// Get the name of the route and explode it into an array
			$requestName = request()->route()->getName();
			$requestArr = explode('.', $requestName);

			// If the array is more than 2 parts, unset the first
			if(count($requestArr) > 2)
				unset($requestArr[0]);

			// If there is only 1 part, we are on the index
			if(count($requestArr) == 1)
				$requestArr[] = 'index';

			// Create the trans key for the help page
			$helpPage = implode('|', $requestArr);
		}

		// Send the correct page to the view
		$view->with(['helpPage' => $helpPage]);
	}
}