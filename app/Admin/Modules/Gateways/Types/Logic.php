<?php

namespace App\Admin\Modules\Gateways\Types;

use App\Models\AirConnect\Gateway as GatewayModel;
use App\Admin\Helpers\Messages;

class Logic
{
	/**
	 * Checking the type of the gateway and getting an object of the API or null if no API
	 * @param array $gateway
	 * @return null
	 */
	public static function getGatewayApiObject(Array $gateway)
	{

		$gatewayApiObj = '\App\Admin\Modules\Gateways\Types\\' .ucwords( strtolower($gateway['type']));

		// If the gateway type does not exist, return null
		if(class_exists($gatewayApiObj))
			return new $gatewayApiObj($gateway);
		else
			return null;
	}

	/**
	 * Rebooting the Gateway
	 * @return string
	 */
	public static function rebootGateway()
	{
		$data 	= \Request::all();
		$name 	= $data['name'];
		$mac 	= $data['mac'];
		$reason = $data['reason'];

		$gateway = GatewayModel::getGatewayFromMac($mac);

		$gatewayApiObject = self::getGatewayApiObject($gateway->toArray());

		if(!is_null($gatewayApiObject))
		{
			Messages::create(Messages::SUCCESS_MSG, trans('admin.gateway'). '"' .$name . ' ('.$mac.') "' . trans('admin.is-restarted'). ' : ' . $reason, false, true);
			return($gatewayApiObject->reboot());
		}
		else
		{
			// This should never happen, since the reboot button won't even show for non API gateways
			return 'No API Found';
		}

	}


	/**
	 * Calling the AAA functionality and turn it on or off depending on parameters
	 * @return string
	 */
	public static function aaaGateway()
	{
		$data 	= \Request::all();
		$name 	= $data['name'];
		$mac 	= $data['mac'];
		$status = $data['status'] == 'enabled'? true: false;
		$reason = $data['reason'];

		$gateway = GatewayModel::getGatewayFromMac($mac);

		$gatewayApiObject = self::getGatewayApiObject($gateway->toArray());

		if(!is_null($gatewayApiObject))
		{
			Messages::create(Messages::SUCCESS_MSG, trans('admin.gateway'). '"' .$name . ' ('.$mac.') "' . trans('admin.aaa-'. $data['status']). ' : ' . $reason, false, true);
			return($gatewayApiObject->turnAAA($status));
		}
		else
		{
			// This should never happen, since the reboot button won't even show for non API gateways
			return 'No API Found';
		}

	}

	/**
	 * Get a list with the Walled Garden entries set on the Mikrotik gateway
	 * @return string
	 */
	public static function getWalledGarden($mac)
	{

		$gateway = GatewayModel::getGatewayFromMac($mac);

		$gatewayApiObject = self::getGatewayApiObject($gateway->toArray());

		if(!is_null($gatewayApiObject))
		{
			return($gatewayApiObject->getWalledGardenList());
		}
		else
		{
			return 'No API Found';
		}

	}

	/**
	 * Get a list with the Walled Garden entries set on the Mikrotik gateway
	 * @return string
	 */
	public static function addWalledGardenEntry($mac, $host, $comment, $action)
	{

		$gateway = GatewayModel::getGatewayFromMac($mac);

		$gatewayApiObject = self::getGatewayApiObject($gateway->toArray());

		if(!is_null($gatewayApiObject))
			return($gatewayApiObject->addWalledGarden($host, $comment, $action));
		else
			return false;

	}

	/**
	 * Get a list with the Walled Garden entries set on the Mikrotik gateway
	 * @param string $mac
	 * @param string $ruleId
	 * @return string
	 */
	public static function removeWalledGardenEntry($mac, $ruleId)
	{

		$gateway = GatewayModel::getGatewayFromMac($mac);

		$gatewayApiObject = self::getGatewayApiObject($gateway->toArray());

		if(!is_null($gatewayApiObject))
			return($gatewayApiObject->removeWalledGarden($ruleId));
		else
			return false;

	}
}