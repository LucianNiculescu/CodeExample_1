<?php
namespace App\Admin\Modules\Locations;

use App\Models\AirConnect\Location as LocationModel;
use Illuminate\Routing\Controller as BaseController;
use App\Admin\Modules\Locations\Logic as Locations;
use \App\Admin\Helpers\Messages;

/**
 * Class Locations Controller
 */
class Controller extends BaseController
{
	/**
	 * Create from the post form
	 */
	public function store()
	{
		return Logic::saveForm();
	}


	/**
	 * Update the specified Location
	 */
	public function update($id)
	{
		return Logic::saveForm($id);
	}


	/**
	 * Soft delete
	 * @param  int  $id
	 * @return int |\Illuminate\Http\RedirectResponse
	 */
	public function destroy($id)
	{
		return Logic::softDelete($id);
	}


	/**
	 * Create the create form
	 */
	public function create()
	{
		// Setup the form's action and method
		$actionUrl = '/manage/locations';
		$hiddenMethod = 'POST';

		// Get the Active Portals for this site as ID => Name Pairs
		$portals = Locations::getPortals(session('admin.site.loggedin'));

		// Data to be sent to the Role edit page
		$data = [
			'title' 		=> trans('admin.location-create'),
			'description' 	=> trans('admin.location-create-desc'),
			'siteId' 		=> session('admin.site.loggedin'),
			'module' 		=> null,
			'hiddenMethod' 	=> $hiddenMethod,
			'actionUrl' 	=> $actionUrl,
			'types' 		=> Locations::getTranslatedLocationTypes(), //Locations::$locationTypes,
			'portals' 		=> $portals,
			'selectedPortal'=> ( $portals->isEmpty() ) ? null : array_keys($portals->toArray())[0],
			'selectedType' => 'guest'
		];

		// Show the edit form and pass the data
		return view('admin.modules.locations.create', $data);
	}

	/**
	 * Show the form for editing the specified Locations.
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit($id)
	{
		// Find the needed resource
		$location = LocationModel::find($id);
		if(empty($location)){
			Messages::create(Messages::ERROR_MSG, trans('admin.location-not-found'));
			return redirect('/manage/locations/');
		}

		// Checking if location site is in the user's estate
		if(!in_array($location->site_id, session('admin.site.estate')))
			abort('401', trans('error.not-authorized'));

		// Setup the form's action and url
		$actionUrl =  '/manage/locations/'.$id;
		$hiddenMethod = 'PUT';

		// Get the Active Portals for this site as ID => Name Pairs
		$portals = Locations::getPortals(session('admin.site.loggedin'));

		// Get a CSV of ports/VLANs for this location
		$portValue = Locations::getCsvOfPorts($id); // '22, 33, 44';

		// Data to be sent to the Role edit page
		$data = [
			'title' 		=> trans('admin.location-edit'),
			'description' 	=> trans('admin.location-edit-desc'),
			'siteId' 		=> session('admin.site.loggedin'),
			'module' 		=> $location,
			'hiddenMethod' 	=> $hiddenMethod,
			'actionUrl' 	=> $actionUrl,
			'cancelUrl' 	=> '/manage/locations/',
			'types' 		=> Locations::getTranslatedLocationTypes(), //Locations::$locationTypes,
			'portals' 		=> $portals,
			'portValue' 	=> $portValue
		];

		// Show the edit form and pass the data
		return view('admin.modules.locations.create', $data);
	}


	/**
	 * Display a listing of the Locations
	 */
	public function index()
	{
		$columns = [
			'name'		=> '',
			'room'		=> '',
			'type'		=> '\App\Admin\Helpers\Datatables\TranslateColumn',
			'updated'	=> '\App\Admin\Helpers\Datatables\DateColumn',
		];

		$data = [
			'title' 				=> trans('admin.locations'),
			'description'			=> trans('admin.locations-desc'),
			'columns'				=> $columns,
			'rows' 					=> Datatable::getLocationsQuery(),
			'tableId' 				=> 'manage-locations', // Must be a valid CSS ID, falles back to module if not exists
			'route'				    => 'manage/locations',
			'hideCreate' 			=> null, // true to hide, null to show the create button
			'showActions'           => Datatable::showActions('manage/locations'), // true, // Check permissions
		];

		// Client side datatable
		return view('admin.modules.locations.client-side-index', $data);
	}
}