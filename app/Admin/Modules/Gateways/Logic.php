<?php
namespace App\Admin\Modules\Gateways;

use App\Models\AirConnect\Package as PackageModel;
use App\Models\AirConnect\Gateway as GatewayModel;
use App\Models\AirHealth\Hardware as HardwareModel;

class Logic
{
	/**
	 * Returns a list of gateways based on a given packageId
	 * @param $packageId
	 * @return mixed
	 */
	public static function getGatewaysByPackage($packageId) {

		$types = [];
		$package = PackageModel::where('id',$packageId)->first();

		$gateways = $package->parentSite->gateways			// Build a list of Gateways (of unique type)
		->reject(function($gateway) use (&$types) {
			if(in_array($gateway->type, $types))
				return true;

			$types[] = $gateway->type;
		})->mapToAssoc(function($gateway) {
			return [$gateway->id, implode(' - ', [$gateway->type, $gateway->name])];
		});

		return $gateways;
	}

	/**
	 * Get Gateway details
	 * Filling the array to show in the show section
	 * @param $id
	 * @return array
	 */
	public static function getDetails($id)
	{
		$ignoreHardwareColumns = ['id', 'location', 'created', 'password'];
		$results = [];
		$gateway = GatewayModel::where('id', $id)
			->select('id', 'type', 'ip', 'mac', 'site')
			->with(['site' => function($q){
				$q->select('id', 'name');
			}])
			->with(['attributes' => function($q){
				$q->select('id', 'ids', 'name', 'type', 'value');
			}])
			->first()->toArray();

		$hardware = HardwareModel::where('mac', $gateway['mac'])
			->first();

		if(is_null($hardware))
		{
			$hardware = [];
			$results['nasid'] 	= trans('admin.n-a');
			$results['mac'] 	= $gateway['mac'];
			$results['ip'] 		= $gateway['ip'];
			$results['type'] 	= $gateway['type'];
		}
		else
		{
			$hardware = $hardware->toArray();

			foreach(array_keys($hardware) as $key)
			{
				if(!in_array($key, $ignoreHardwareColumns))
				{
					if($key == 'password')
					{
						$results[$key] = '<span style="color:transparent">'.$hardware[$key].'</span>';
					}
					else if($hardware[$key] != '' and $hardware[$key] != '0' )
					{
						$results[$key] = $hardware[$key];
					}
				}
			}
		}

		$results['site'] 			= '<strong>' . $gateway['site']['id'] . '</strong>: ' . $gateway['site']['name'];
		$results['configured-ip'] 	= $gateway['ip'];

		if(!empty($gateway['attributes']))
		{
			foreach($gateway['attributes'] as $attributes)
			{
				$results['attributes'][] = '<strong>' . $attributes['name'] . '</strong>: ' . $attributes['value'];
			}
		}

		return $results;
	}

	/**
	 * Checks if the type of the gateway is MIKROTIK
	 * This can get an array of Gateways or a Gateway object
	 * Returns an array with the supported gateways or Gateway object
	 *
	 * @param $gateways
	 * @return mixed
	 */
	public static function getSupportedGateways($gateways) {
		$supportedGateways = [];

		//ckeck if it's an array and build
		if(count($gateways) > 0) {
			foreach($gateways as $gate) {
				//check if it's an object
				if(is_object($gate)){
					if(self::checkGatewayType($gate->type))
						$supportedGateways[] = $gate;
				} elseif(is_array($gate)) { //checks if gate is array
					if(self::checkGatewayType($gate['type']))
						$supportedGateways[] = $gate;
				}
			}
		}
		elseif(is_a($gateways, 'Gateway')) {
			if(self::checkGatewayType($gateways->type))
				return $gateways;
		}


		return $supportedGateways;

	}

	/**
	 * Checks if the type is MIKROTIK
	 * //TODO: ADD MORE GATEWAY TYPES AS WE ADD THEM TO SYSTEM
	 *
	 * @param $type
	 * @return bool
	 */
	public static function checkGatewayType($type) {
		if($type == "MIKROTIK")
			return true;
		return false;
	}
}