<?php
namespace App\Admin\Modules\Locations;

use \App\Admin\Helpers\Messages;
use App\Models\AirConnect\Location as LocationModel;
use App\Models\AirConnect\Portal as PortalModel;
use App\Models\AirConnect\Vlan as VlanModel;
use App\Models\AirConnect\Location;
use App\Models\AirConnect\Portal;
use App\Admin\Middleware\Access;

class Logic
{
	// List of location types - NB. Should be translated before use on the UI using getTranslatedLocationTypes()
	public static $locationTypes = [
		'guest',
		'event',
		'venue'
	];


	/**
	 * Gets a list of location types but adds the translated version as a value and the name as the key
	 * @return array
	 */
	public static function getTranslatedLocationTypes()
	{
		// Set the return var array
		$translatedLocationTypes = [];

		// Loop through all the types
		foreach (self::$locationTypes as $locationType)

			// Add the name as the key and the translation as the value
			$translatedLocationTypes[$locationType] = trans('admin.' .$locationType);

		// Return the key/value pairs
		return $translatedLocationTypes;
	}


	/**
	 * Changes the status to deleted
	 * @param $id
	 * @return Logic|\Illuminate\Http\RedirectResponse
	 */
	public static function softDelete($id)
	{
		return self::saveForm($id, 'deleted');
	}


	/**
	 * Saves location from form or status
	 * @param null $id
	 * @param null $status
	 * @return int |\Illuminate\Http\RedirectResponse
	 */
	public static function saveForm($id = null, $status = null)
	{
		// Request data
		$requestData = \Request::all();

		// Save rules for the form
		if (!\Request::ajax()) {

			$rules = [
				'name' 		=> 'required|min:3|max:60',
				'room_no' 	=> 'required|max:45',
				'type' 		=> 'required',
				'site_id' 	=> 'required'
			];

			// Validate
			$validator = \Validator::make($requestData, $rules);

			// If validation fails, return back and refill all fields
			if ($validator->fails())
				return \Redirect::back()
					->withErrors($validator)->withInput();
		}

		// If status is sent as a parameter then add it to the $requestData array
		if (!is_null($status))
			$requestData["status"] = $status;

		// Create
		if (is_null($id)){
			$location = LocationModel::create($requestData);

		// Update
		}else{
			$location = LocationModel::find($id);
			$location->update($requestData);
		}

		// Ajax returns 1 if successful
		if(\Request::ajax())
			return 1;

		// $requestData['ports'] contains a comma separated list, we must turn to array, format it and save as VLAN
		if(isset($requestData['ports']))
			self::saveCsvToVlan($requestData['ports'], $location->id);

		// Tell the user
		Messages::create(Messages::SUCCESS_MSG, trans('admin.location-saved'));

		// If user doesn't have access to edit go to index page
		if(\Gate::denies('access', Access::permissionFromPath(\Request::path() . '/edit')))
			return redirect(\Request::path());
		else
			return redirect()->route('manage.locations.edit', $location->id);
	}


	/**
	 * Get a collection of VLANs from the DB and implode to CSV
	 * @param $locationId
	 * @return string
	 */
	public static function getCsvOfPorts($locationId)
	{
		// Get the Ports
		$ports = VlanModel::where('location_id', $locationId)->get();

		// If there is none, send ''
		if($ports->isEmpty())
			return '';

		// If there is only 1 send just this
		if($ports->count() == 1)
			$ports->first()->vlan;

		// implode and return
		return $ports->implode('vlan', ', ');
	}


	/**
	 * Save a CSV of strings to the DB as VLANs
	 * @param string $portsCSV
	 * @param $locationId
	 */
	public static function saveCsvToVlan(string $portsCSV, $locationId)
	{
		// Make CSV to array
		$ports = explode(',', $portsCSV);

		// Make the array to add into VLAN
		$vlans = [];
		foreach ($ports as $port)
		{
			$vlans[] = [
				'location_id' 	=> $locationId,
				'vlan' 			=> trim($port)
			];
		}

		// Delete all VLANs for this location
		VlanModel::where('location_id', $locationId)->delete();

		// Create the VLANs for this location
		VlanModel::insert($vlans);
	}


	/**
	 * Get the Active Portals for this site as ID => Name Pairs
	 * @param $siteId
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector | Portal
	 */
	public static function getPortals($siteId)
	{
		// Get the Active Portals for this site as ID => Name Pairs
		$portals = PortalModel::where(['site' => $siteId, 'status' => 'active'])->get()->pluck('name','id');

		// If there are no active Portals
		if(empty($portals)){
			Messages::create(Messages::ERROR_MSG, trans('admin.portals-not-found'));
			abort(302, ['Location'=>'/manage/locations/']);
		}

		//Return the active Portals
		return $portals;
	}


	/**
	 * @param $siteId
	 * @return int
	 */
	public static function count($siteId)
	{
		return Location::where('site_id', $siteId)->count();
	}


	public static function getSiteLocations($siteId)
    {
        return LocationModel::where('site_id', $siteId)
			->where('status', '!=', 'deleted')
			->get()->pluck('name','id');
    }

    public static function getPortalLocations($portalId)
    {
        $portalWithLocations = PortalModel::where(['id' => $portalId])->with('locations')->first();

        //dd($portalWithLocations->locations[0]->name);
        $result = [];

        foreach ($portalWithLocations->locations as $portalWithLocation)
        {
            $result[$portalWithLocation->id] = $portalWithLocation->name;
        }

        return $result;
    }

	/**
	 * Getting all ports from a given portal id
	 * @param $portalId
	 * @return array
	 */
    public static function getPortsFromPortal($portalId)
	{
		// Getting vlan information from a given portal
		$portalWithLocations = PortalModel::where(['id' => $portalId])
			->with(['locations' => function($q){$q->with('vlan');}])
			->first()
			->toArray();

		$ports = [];

		// Looping into the locations to build the result array with ports
		foreach($portalWithLocations['locations'] as $location)
		{
			if(!empty($location['vlan']))
			{
				if(gettype($location['vlan'] == 'array'))
				{	// location has many ports
					foreach($location['vlan'] as $vlan)
					{
						$ports[] = $vlan['vlan'];
					}
				}
				else
				{	// location has only one port
					$ports[] = $location['vlan']['vlan'];
				}
			}
		}

		return $ports;
	}

}