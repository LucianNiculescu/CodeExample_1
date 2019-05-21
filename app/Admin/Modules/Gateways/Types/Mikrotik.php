<?php

namespace App\Admin\Modules\Gateways\Types;

use App\Helpers\CurlHelper;
use MikrotikAPI\Commands\IP\Firewall\FirewallMangle;
use MikrotikAPI\Commands\IP\Hotspot\HotspotServer;
use MikrotikAPI\Commands\IP\Hotspot\HotspotUserProfiles;
use MikrotikAPI\Talker\Talker;
use MikrotikAPI\Entity\Auth;
use MikrotikAPI\MikrotikException;
use MikrotikAPI\Commands\IP\Hotspot\Hotspot;
use MikrotikAPI\Util\SentenceUtil;


class Mikrotik Extends SuperType
{
	protected $talker;
	protected $hotspot;
	protected $hotspotServer;
	/**
	 *
	 * Mikrotik constructor.
	 * @param $gateway
	 */
	function __construct($gateway)
	{
		parent::__construct($gateway);
		$this->authenticate();

		// Mapping mikrotik specific information with onlinedata information
		$this->onlineNowInterpreter = [
			'bytes-in'			=> 'upload',
			'bytes-out'			=> 'download',
			'uptime' 			=> 'time',
			'mac-address'		=> 'mac',
			'bridge-port'		=> 'vlan',
		];

		// Mapping mikrotik specific information for Logs
		$this->logsInterpreter = [
/* No need to use interpretter her, but this is just an example, put the keys as they are shown from the API, and the values as the way we need them in the datatable
			'time-xxx' 				=> 'time',
			'topics-xxx' 			=> 'topics',
			'message-xxx' 			=> 'message'
*/
		];
	}

	/**
	 * Gets online now data from the API, i.e. substable
	 * @return mixed
	 */
	public function getConnectedDevices()
	{
		try {

			$hosts = $this->getAllHosts();

			if(is_array($hosts))
				return $this->interpretResult($hosts, $this->onlineNowInterpreter);
			else
				return $hosts;

		} catch (MikrotikException $e) {
			return $e->getMessage();
		} catch(\Throwable $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Getting logs from the gateway
	 * @return string|array
	 */
	public function getLogs()
	{
		try {
			$this->runCommand("/log/print");

			$logs = $this->talker->getResult()->getResultArray();

			// Returning interpreted results
			return $this->interpretResult($logs, $this->logsInterpreter);

		} catch (MikrotikException $e) {
			return $e->getMessage();
		} catch(\Throwable $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Sign out connected device
	 * @param $id
	 * @return int|string
	 */
	public function signOut($id)
	{
		try {
			$this->hotspot = new Hotspot($this->talker);

			$this->hotspot->active()->delete($id);

			return 1;

		} catch (MikrotikException $e) {
			return $e->getMessage();
		} catch(\Throwable $e) {
			return $e->getMessage();
		}
	}


	/**
	 * Getting AAA status
	 * @return int|string
	 */
	public function getAAAStatus()
	{
		try {
			$server = $this->getHotSpotServer();

			if(!is_array($server))
				return $server;

			$status = $server['disabled'];

			if($status == 'true')
				return 1;
			else
				return 0;

		} catch (MikrotikException $e) {
			return $e->getMessage();
		} catch(\Throwable $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Enable AAA, i.e. turn the portal on and
	 * @param $status
	 * @return int|string
	 */
	public function turnAAA($status)
	{
		try {

			$server = $this->getHotSpotServer();

			if(!is_array($server))
				return $server;

			if($status)
				$this->hotSpotServer->enable($server['.id']);
			else
				$this->hotSpotServer->disable($server['.id']);

			return 1;

		} catch (MikrotikException $e) {
			return $e->getMessage();
		} catch(\Throwable $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Get the name of the gateway
	 * @return mixed
	 */
	public function getGatewayName()
	{
		try {
			// The gateway name can be obtained through the sentence '/system/identity/print'
			$this->runCommand("/system/identity/print");
			$result = $this->talker->getResult()->getResultArray();
			// Extract the name from the array and return it
			return $result[0]['name'];

		} catch (MikrotikException $e) {
			\Log::info($e->getMessage());
			return $e->getMessage();
		} catch(\Throwable $e) {
			\Log::info($e->getMessage());
			return $e->getMessage();
		}
	}


	/**
	 * Check whether the name of the gateway matches the supplied name and return the outcome
	 * @param string $nameToCheck - the name we expect from the gateway
	 * @return boolean
	 */
	public function matchingGatewayName($nameToCheck)
	{
		$gatewayName = $this->getGatewayName();
		return ($nameToCheck === $gatewayName);
	}


	/**
	 * Enable AAA, i.e. turn the portal on and
	 * @return mixed
	 */
	public function getWalledGardenList()
	{
		try {
			$this->runCommand("/ip/hotspot/walled-garden/print");
			return $this->talker->getResult()->getResultArray();

		} catch (MikrotikException $e) {
			return $e->getMessage();
		} catch(\Throwable $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Add a Walled Garden entry
	 * @param string $host
	 * @param string $comment
	 * @param string $action
	 * @return bool
	 */
	public function addWalledGarden($host, $comment, $action) {
		try {
			$this->addCommand("/ip/hotspot/walled-garden/add", [
				'dst-host' 	=> $host,
				'comment'	=> $comment,
				'action'	=> $action
			]);
			return true;

		} catch (MikrotikException $e) {
			return $e->getMessage();
		} catch(\Throwable $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Removes a Walled Garden entry
	 * @param string $id
	 * @return bool
	 */
	public function removeWalledGarden($id) {
		try {
			$this->removeCommand("/ip/hotspot/walled-garden/remove", $id);
			return true;
		} catch (MikrotikException $e) {
			return $e->getMessage();
		} catch(\Throwable $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Rebooting the gateway
	 * @return string
	 */
	public function reboot()
	{
		try {
			$this->runCommand("/system/reboot");
			return 1;

		} catch (MikrotikException $e) {
			return $e->getMessage();
		} catch(\Throwable $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Enable/disable adjets per gateway
	 * @param bool $status
	 * @return mixed
	 */
	public function toggleAdjetsStatus($status) {

		try {
			$this->toggleFirewallMangleByComment($status,'Squid WCCP Hello Packet');
			if($status)
				$this->setHotspotUserProfileAttribute('default', ['transparent-proxy' => 'yes']);
			else
				$this->setHotspotUserProfileAttribute('default', ['transparent-proxy' => 'no']);

		} catch (MikrotikException $e) {
			return $e->getMessage();
		} catch(\Throwable $e) {
			return $e->getMessage();
		}

		return true;
	}

	/**
	 * Enables / Disables a Firewall Mangle rule based on a given status and comment
	 * @param bool $status
	 * @param string $comment
	 * @return bool
	 */
	private function toggleFirewallMangleByComment($status, $comment) {
		//Get all the Firewall Mangle rules
		$mangle = new FirewallMangle($this->talker);
		$mangleRules = $mangle->getAll();

		//Loop through them and find the one having the comment == $comment
		if(!empty($mangleRules) && is_array($mangleRules)) {
			foreach($mangleRules as $rule) {
				if(!empty($rule['comment']) && $rule['comment'] == $comment) {
					//Enable/Disable the rule
					if($status)
						$mangle->enable($rule['.id']);
					else
						$mangle->disable($rule['.id']);
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Enables / Disables a Hotspot User Profile rule based on a given comment and status
	 * @param string $name
	 * @param array $attribute
	 * @return bool
	 */
	private function setHotspotUserProfileAttribute($name, $attribute) {
		//Get all User Profile attributes
		$userProfile = new HotspotUserProfiles($this->talker);
		$attributes = $userProfile->getAll();

		//Loop through them and find the attribute with name == $name
		if(!empty($attributes) && is_array($attributes)) {
			foreach($attributes as $attr) {
				if(!empty($attr['name']) && $attr['name'] == $name) {
					//Set 'transparent-proxy' to 'yes' or 'no', based on the status
					$userProfile->set( $attribute, $attr['.id']);
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Authenticate to the gateway using the ip, username and password from the gateway private variable
	 */
	private function authenticate()
	{
		$auth = new Auth($this->gateway['ip'], $this->gateway['username'], $this->gateway['password']);
		$this->talker = new Talker($auth);
	}

	/**
	 * Getting all Devices connected to the gateway
	 * @return mixed
	 */
	private function getAllHosts()
	{
		//return null;
		$this->hotspot = new Hotspot($this->talker);

		$startTime = time();
		// Getting Hosts i.e. guest devices
		$guestDevices  = $this->hotspot->hosts()->getAll();
		$endTime = time();

		// If time to get hosts was 20 secounds or more then return a timed-out message
		if($endTime - $startTime >= 20)
			return trans('admin.gateway-timed-out');


		if(!is_array($guestDevices))
			$guestDevices = [];

		$activeDevices = $this->hotspot->active()->getAll();

		if(!is_array($activeDevices))
			$activeDevices= [];

		$activeDevicesByMac = [];

		foreach($activeDevices as $activeDevice)
			$activeDevicesByMac[$activeDevice['mac-address']] = $activeDevice;


		foreach($guestDevices as &$guestDevice)
			if(in_array($guestDevice['mac-address'], array_keys($activeDevicesByMac)))
				$guestDevice['.id'] = $activeDevicesByMac[$guestDevice['mac-address']]['.id'];

		return $guestDevices;
	}


	private function getHotSpotServer()
	{
		$this->hotSpotServer = new HotspotServer($this->talker);

		$allServers = $this->hotSpotServer->getAll() ; // it is mostly only 1

		if(!is_array($allServers))
			return trans('error.gateway-connection');

		// If it is only 1 server just return it
		if(sizeof($allServers) == 1)
			return $allServers[0];
		else
			// Loop in the hotspot servers and check if the name is equal to the mac or not
			foreach ($allServers as $server)
				if($server['name'] == $this->gateway['mac'])
					return $server;

		// If no server found, return an error
		return trans('error.gateway-connection');
	}

	private function runCommand($command)
	{
		$sentence = new SentenceUtil();
		$sentence->fromCommand($command);
		$this->talker->send($sentence);
	}

	/**
	 * This function is used to add for a command
	 * @param string $command
	 * @param array $params
	 * @return bool
	 */
	private function addCommand($command, $params) {
		try {
			$sentence = new SentenceUtil();
			$sentence->addCommand($command);

			foreach ($params as $name => $value) {
				$sentence->setAttribute($name, $value);
			}
			$this->talker->send($sentence);
			return true;

		} catch (MikrotikException $e) {
			return $e->getMessage();
		} catch(\Throwable $e) {
			return $e->getMessage();
		}
	}

	/**
	 * This method is used to delete by a command and a ruleid
	 * @param string $command
	 * @param string $id
	 * @return mixed
	 *
	 */
	private function removeCommand($command, $id) {
		try {
			$sentence = new SentenceUtil();
			$sentence->addCommand($command);
			$sentence->where(".id", "=", $id);
			$this->talker->send($sentence);
			return true;

		} catch (MikrotikException $e) {
			return $e->getMessage();
		} catch(\Throwable $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Change of Authorisation
	 * Changing the gateway bandwidth on live devices having a guid
	 *
	 * @param $upload
	 * @param $download
	 * @param $guid
	 * @param string $port
	 * @return array|bool|\Illuminate\Contracts\Translation\Translator|null|string
	 */
	public function changeBandwidth($upload, $download, $guid, $port = '') {

		try {
			//Set the errors as an array
			$errors = [];

			$radius = $this->getRadiusServer();
			if(empty($radius))
				return $errors[] = trans('admin.no-radius-server');

			//Get the list of users by GUID
			$users = $this->getUsersByGuid($guid);

			if(empty($users))
				return true;

			//Set the params
			$radiusIp 		= $radius[0]['address'];
			$radiusSecret 	= $radius[0]['secret'];
			$port 			= (!empty($port))? $port : config('services.change_of_auth.port');//If we don't have the port, use the default one set in the config file
			$url 			= 'http://'.$radiusIp.':8080/coa/';
			$parameters 	= [];

			//Set the bandwidth to all the devices of the user
			foreach($users as $user) {
				$parameters = [
					'aaa-params' 	=> [
						'Mikrotik-Rate-Limit' => $upload . 'k/' . $download.'k'
					],
					'gateway' 		=> [
						'port'		=> $port,
						'secret' 	=> $radiusSecret,
						'ip'		=> $this->gateway['ip']
					],
					'host' => $user['address']
				];

				//API call to the radius server with all the data that we have to change the bandwidth
				$response = json_decode(CurlHelper::postJson($url, $parameters));
				if($response->code !== 'ACK')
					$errors[0] = trans('admin.coa-guid-failed', ['guid' => $guid]);
			}

			if(empty($errors))
				return true;
			else
				return $errors;

		} catch (MikrotikException $e) {
			return $e->getMessage();
		} catch(\Throwable $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Get the Radius server that this gateway is using
	 * @return string
	 */
	private function getRadiusServer() {
		$radius = new SentenceUtil();
		$radius->fromCommand('/radius/print');
		$radius->where('service', '=', "hotspot");
		//Get the result
		$this->talker->send($radius);
		return $this->talker->getResult()->getResultArray();
	}

	/**
	 * Get the list of users by a GUID
	 * @param $guid
	 * @return mixed
	 */
	private function getUsersByGuid($guid) {
		//Get the IP of the User by it's GUID
		$users = new SentenceUtil();
		$users->fromCommand('/ip/hotspot/active/print');
		$users->where('user', '=', $guid);
		//Get the result
		$this->talker->send($users);
		return $this->talker->getResult()->getResultArray();
	}

	public function getWanThroughputData() {
		try {
			//Get the data from the first ethernet interface
			$wan1 = new SentenceUtil();
			//Using the addCommand instead of fromCommand() because monitor-traffic apparently doesn't want to work with that one
			$wan1->addCommand('/interface/monitor-traffic');
			//This doesn't have the operator because we are not actually setting anything (so instead we are using an attribute)
			$wan1->setAttribute('interface','ether1-WAN1');
			//This is a strange parameter but without a value, it works
			$wan1->setAttribute('once', '');

			//Get the result of the first interface
			$this->talker->send($wan1);
			$resultWan1 = $this->talker->getResult()->getResultArray();

			//Get the data from the second ethernet interface
			$wan2 = new SentenceUtil();
			//Using the addCommand instead of fromCommand() because monitor-traffic apparently doesn't want to work with that one
			$wan2->addCommand('/interface/monitor-traffic');
			//This doesn't have the operator because we are not actually setting anything (so instead we are using an attribute)
			$wan2->setAttribute('interface','ether2-WAN2');
			//This is a strange parameter but without a value, it works
			$wan2->setAttribute('once', '');
			//Get the result of the second interface
			$this->talker->send($wan2);
			$resultWan2 = $this->talker->getResult()->getResultArray();

			//Create the returned array
			$data = [];
			array_push($data, [
				"wan" => "1",
				'rx' => (int)$resultWan1[0]['rx-bits-per-second']/1000000,
				'tx' => (int)$resultWan1[0]['tx-bits-per-second']/1000000
			],[
				"wan" => "2",
				'rx' => (int)$resultWan2[0]['rx-bits-per-second']/1000000,
				'tx' => (int)$resultWan2[0]['tx-bits-per-second']/1000000
			]);

			return json_encode($data);

		} catch (MikrotikException $e) {
			return $e->getMessage();
		} catch(\Throwable $e) {
			return $e->getMessage();
		}
	}
}


