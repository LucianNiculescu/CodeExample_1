<?php

namespace App\Admin\Helpers\Composers;

use Illuminate\Support\Facades\Auth;
use App\Models\AirConnect\Site;

class BreadcrumbComposer {

    public function compose($view)
    {

        //get the site path from the session
        $sites = session('admin.site.path');

        // if a path exists
        if($sites != null) {
			// get the current url and pass, explode it
			$currentRoute = \Request::path();
			$exploded_current_url = explode("/",$currentRoute);

			//set the $route variable that we will be using as breadcrumb and get the system variables that are sent through the route
			$route = [];
			$routeParams = \Route::current()->parameters();
			foreach ($exploded_current_url as $key => $val){
				//check if the value is an input parameter for the controller (ex: siteId/language/userId...) and remove them
				if(in_array($val, $routeParams)) {
					unset($exploded_current_url[$key]);
				} else {
					//set the text displayed and an empty url
					$route[$key]['title'] = $exploded_current_url[$key];
					$route[$key]['url'] = '';
				}
			}

			//get the menu from the config file
			$menu = config('menu.admin.main');
			if(isset($menu[$exploded_current_url[0]])) {
				//get the user to check the permissions
				$user = Auth::user();
				//if the page is not static
				if(isset($menu[$exploded_current_url[0]]['links'])) {
					foreach($menu[$exploded_current_url[0]]['links'] as $key=>$val) {
						$permission = $exploded_current_url[0].'.'.$key;
						//Check the permissions or remove them from menu
						if($user->cannot('access' ,$permission )) {
							unset($menu[$exploded_current_url[0]]['links'][$key]);
						}

						if($key === 'adjets' && !\App\Admin\Modules\AdJets\Logic::checkEnabledAdJets())
							unset($menu[$exploded_current_url[0]]['links'][$key]);

						if($key === 'vouchers' && !in_array('voucher', session('admin.site.active_package_types', [])))
							unset($menu[$exploded_current_url[0]]['links'][$key]);
					}

					//if the user has permissions for those pages, set them as options
					if(count($menu[$exploded_current_url[0]]['links']) > 0) {
						$route[0]['hasDropdown'] = true;
						$route[0]['options']	 = $menu[$exploded_current_url[0]]['links'];
					}
				}
			}

			// if there are more than 2 breadcrumbs, we will add the correct url
			if(count($exploded_current_url) > 2) {
				$route[1]['text'] = $exploded_current_url[1];
				$route[1]['url']  = $exploded_current_url[0].'/'.$exploded_current_url[1];
			} else if(count($exploded_current_url) == 2) {
				$route[0]['text'] = $exploded_current_url[0];
				$route[0]['url']  = $exploded_current_url[0];
			}

			// sets the new route without the numeric segments
			$view->with('route', $route);
			// sets the last entry of the new route as the current page
			$view->with('currentPage', end($route));

            // Get all sites that match the
            $allSitesInPath = Site::whereIn('id', $sites)->get();
            $view->with('siteData', ['sitesAvailable' => 'yes', 'sites' => $allSitesInPath]);
            $count = count($allSitesInPath);
            $view->with('count', $count);

            // change from object to array
            $sites_array = $allSitesInPath->toArray();

            //search array for an record with the same id as the current site id
            $key = array_search(session('admin.site.loggedin'), array_column($sites_array, 'id'));
            $view->with('sites_array', $sites_array);
            $view->with('key', $key);

        }
        else{
            $view->with('siteData', ['sitesAvailable' => 'no']);
        }
    }
}