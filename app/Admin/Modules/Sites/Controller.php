<?php
    namespace App\Admin\Modules\Sites;

	use App\Admin\Widgets\Prtg as PrtgLogic;
	use Illuminate\Routing\Controller as BaseController;
	use \App\Admin\Modules\Sites\Logic as Sites;
	use App\Models\AirConnect\Site as SiteModel;

	/**
	 * Class Controller
	 * @package App\Admin\Modules\Sites\Controller
	 */
	class Controller extends BaseController
	{
		/**
		 * Sending Estate true flag to the index function
		 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
		 */
		public function showEstateView()
		{
			return $this->index(true);
		}

		/**
		 * Display a listing of the Sites
		 * @param bool $estatePage
		 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
		 */
		public function index($estatePage = false)
		{
			if( in_array(\Request::path(), ['estate', 'manage/sites']) )
				$estatePage = true;

			$siteIds = session('admin.site.estate');

			// If user has only 1 site in his estate then the estate page will be redirected to the site dashboard
			if(count($siteIds) == 1)
				return \Redirect::to('/dashboard/'.$siteIds[0]);

			return $this->showServerSideDatatable($estatePage);
		}

		/**
		 * Open the estate view and fill it with serverside datatable
		 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
		 */
		public function showServerSideDatatable($estatePage)
		{
			$data = SetupViewData::serverSide($estatePage);

			// If Ajax then it is the site-list widget
			if(\Request::ajax())
				return view('admin.modules.sites.server-side-widget', $data );
			else
				return view('admin.modules.sites.server-side-index' ,$data);
		}

		/**
		 * estate
		 * @return \Illuminate\Contracts\View\View
		 */
		public function showClientSideDatatable()
		{
			$data = SetupViewData::clientSide();

			// If Ajax then it is the site-list widget
			if(\Request::ajax())
				return view('admin.modules.sites.client-side-widget', $data );
			else
				return view('admin.modules.sites.client-side-index' ,$data);
		}

		/**
		 * gets All Sites to send it to the index page via serverside datatable
		 * @return mixed
		 */
		public function getAllSitesDatatable()
		{
			return $this->getSitesDatatable(false); //$estatePage = false
		}

		/**
		 * this is called to get the Json object back to the estate route
		 * @return mixed
		 */
		public function getSitesDatatable($estatePage = true)
		{
			$query = Sites::getSites(false, $estatePage); // $clientSide = false;

			// Return the Datatable json
			return Datatable::makeTable($query, $estatePage);
		}

		/**
		 * Dashboard index controller
		 * @param null $siteId
		 * @return mixed
		 */
		public function dashboard($siteId = null, $redirect = false)
		{
			$site = SiteModel::find($siteId);

			if( isset($site->version) && $site->version != 3 )
				abort('401', trans('error.site-not-upgraded'));

			//If we need to redirect and the previous page was not 'dashboard', redirect to it
			if ( ($redirect == true) && (!Sites::checkBackUrl('dashboard')) ) {
				//Set up Session before with the new $siteId before redirecting
				Sites::setupSession($siteId);

				return redirect()->back();

			}
			return view('admin.modules.sites.dashboard', SetupViewData::dashboard($siteId));
		}

		/**
		 * Display the specified Site. will redirect to dashboard for now
		 * @param  int $id
		 * @return \Illuminate\Http\RedirectResponse
		 */
		public function show($id)
		{
			$modulePath = '/' . \Request::path();
			$length = strpos($modulePath, 'sites') + 5;
			$modulePath = substr($modulePath, 0 , $length);
			return \Redirect::to($modulePath);
		}

		/**
		 * Show the form for creating a new Site.
		 */
		public function create()
		{
			// Open the form view
			return view('admin.modules.sites.form',SetupViewData::create());
		}


		/**
		 * Show the form for editing the specified Site.
		 * @param  int $id
		 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
		 */
		public function edit($id)
		{
			// Show the edit form and pass the data
			return view('admin.modules.sites.form', SetupViewData::edit($id));
		}

		/**
		 * create a site with the posted data from the form
		 */
		public function store()
		{
			$siteCRUD = new CRUD('Site');
			return $siteCRUD->saveForm();
		}


		/**
		 * Update the specified Site
		 */
		public function update($id)
		{
			// if id is : then it is updating the loggedin site
			if($id == ':')
				$id = session('admin.site.loggedin');

			// Upgrade to latest version, if not there already. Otherwise store request.
			return Sites::upgradeOrUpdate($id);
		}

		/**
		 * @param  int  $id
		 * @return \App\Admin\Helpers\RedirectResponse|int
		 */
		public function destroy($id)
		{
		    if($hasChildren = cached_site_service($id)->children()->count() > 0)
		        return response(trans('admin.site-has-children'));

			$siteCRUD = new CRUD('Site');
			return $siteCRUD->delete($id);
		}

		/**
		 * Gets the PRTG Sensors from the given server
		 * Inserts them into prtg_sensors table
		 * Returns the sensor ids as a comma separated list
		 *
		 * @return bool|string
		 */
		public function setUpPrtg() {
			$request = \Request::all();
			if(!empty($request['action']) && $request['action'] == 'create') {
				if(!empty($request['url']) && !empty($request['username']) && !empty($request['passhash'])) {
					//Gets the URL and siteId from the request and unsets them
					$url = $request['url'];
					$siteId = $request['siteId'];
					unset($request['url'], $request['siteId']);
					return PrtgLogic::setUpPrtgServer($siteId, $url, $request);
				}
			} elseif($request['action'] == 'delete')
				return PrtgLogic::deletePrtg($request['siteId']);


			return false;
		}
	}
