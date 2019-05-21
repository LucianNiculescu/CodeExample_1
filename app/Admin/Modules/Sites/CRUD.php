<?php

namespace App\Admin\Modules\Sites;

use \App\Admin\Helpers\BasicCRUD;
use App\Admin\Widgets\Prtg as PrtgLogic;
use App\Jobs\EmailJob;
use App\Models\AirConnect\SiteAttribute as SiteAttributeModel;
use App\Admin\Modules\Sites\Logic as Sites;
use App\Models\AirConnect\Gateway as GatewayModel;
use App\Admin\Modules\Gateways\Types\Logic as Types;

class CRUD extends BasicCRUD
{
    public $rules =
        [

        ];


    // Arrays of attributes categorized as needed , categoy is in the name, in the constructor we use them to setup the site company and estate attributes
    private $createFlag = false;
    private $setAdjetsProperties = true;
    private $setPrtgProperties = true;
    private $adjets = [];
	public 	$attributes = [];
    public 	$siteAttributes = [];
    public 	$companyAttributes = [];
    public 	$estateAttributes = [];
    private $dontDeleteTypes = ['pms', 'upms', 'captive', 'gha', 'voyat'];
    private $dontDeleteAttributes = [
    	'licenses', 'cdn', 'adjets', 'gateway_id', 'package_id', 'venue_id', 'worldpay', 'worldpay_corp_code', 'worldpay_corp_pass',
		'mailchimp', 'mailchimp_mon_api', 'mailchimp_mon_listid', 'campaign_monitor', 'campaign_mon_api', 'campaign_mon_listid', 'nodin', 'pms',
		'sms_provider_inetworx', 'sms_provider_inetworx_auth_user', 'sms_provider_inetworx_auth_pass', 'sms_provider_inetworx_api_user', 'sms_provider_inetworx_api_pass',
		'sms_provider_inetworx_number', 'sms_provider_text_local', 'sms_provider_text_local_username', 'sms_provider_text_local_api_key', 'sms_provider_twilio',
		'sms_provider_dataport', 'sms_provider_dataport_username','sms_provider_dataport_account_no','sms_provider_dataport_password',
		'sms_provider_twilio_account_sid', 'sms_provider_twilio_auth_token', 'sms_provider_twilio_number', 'prtg_api_server', 'prtg_api_username', 'prtg_api_password',
	];

    public $generalAttributes = [
		'sitetype' 			=> '',
		'licenses' 			=> '',
		'number' 			=> '',
		'manager' 			=> '',
		'company_no' 		=> '',
		'currency' 			=> '',
		'pms'				=> '',
	];

    public $siteOnlyAttributes = [
		'support' 			=> '',
		'timezone' 			=> '',
		'pms'	 			=> '',
		'vat_no' 			=> '',
		'vat' 				=> '',

	];

    public $addressAttributes = [
		'address1' 			=> '',
		'address2' 			=> '',
		'town' 				=> '',
		'address' 			=> '',
		'country_code' 		=> '',
	];

    public $campaignAttributes = [
    	'campaign_monitor'		=> '',
    	'campaign_mon_api'		=> '',
		'campaign_mon_listid'	=> '',
	];

    public $mailchimpAttributes = [
		'mailchimp'				=> '',
		'mailchimp_mon_api'		=> '',
		'mailchimp_mon_listid' 	=> '',
	];

	public $adjetsAttributes = [
		'adjets'				=> '',
		'gateway_id'			=> '',
		'package_id'		 	=> '',
		'venue_id'			 	=> '',
	];
	public $prtgAttributes = [
		'prtg_api_server'			=> '',
		'prtg_api_username'		=> '',
		'prtg_api_passhash'		=> '',
	];

	public $cdnAttributes = [
		'cdn'					=> '',
	];

    public $paypalAttributes = [
		'paypal'				=> '',
		'paypal_client_id'		=> '',
		'paypal_secret'      	=> '',
		'paypal_webhook_id'     => '',
	];

    public $worldpayAttributes = [
		'worldpay'			  	=> '',
		'worldpay_corp_code'  	=> '',
		'worldpay_corp_pass'  	=> '',
	];

	public $estateOnlyAttributes = ['roaming'	=> ''];

	public $dataExportAttributes = [
		'nodin' => ''
	];

	public $smsProvidersAttributes = [
		'sms_provider_twilio' 				=> '',
		'sms_provider_twilio_account_sid' 	=> '',
		'sms_provider_twilio_auth_token' 	=> '',
		'sms_provider_twilio_number' 		=> '',
		'sms_provider_text_local' 			=> '',
		'sms_provider_text_local_username' 	=> '',
		'sms_provider_text_local_api_key' 	=> '',
		'sms_provider_inetworx' 			=> '',
		'sms_provider_inetworx_auth_user' 	=> '',
		'sms_provider_inetworx_auth_pass' 	=> '',
		'sms_provider_inetworx_api_user' 	=> '',
		'sms_provider_inetworx_api_pass' 	=> '',
        'sms_provider_inetworx_number' 		=> '',
		'sms_provider_dataport'				=> '',
		'sms_provider_dataport_username'	=> '',
		'sms_provider_dataport_account_no'	=> '',
		'sms_provider_dataport_password'	=> '',
	];


	/**
	 * CRUD constructor.
	 * Constructing the needed Model
	 * Constructing Arrays and the success message
	 * @param $model
	 */
	public function __construct($model)
	{
		parent::__construct($model);

		$this->successMsg = trans('admin.site-saved');

		// Site Attributes
		$this->siteAttributes = $this->generalAttributes
			+ $this->siteOnlyAttributes
			+ $this->addressAttributes
			+ $this->campaignAttributes
			+ $this->mailchimpAttributes
			+ $this->paypalAttributes
			+ $this->worldpayAttributes
			+ $this->adjetsAttributes
			+ $this->prtgAttributes
			+ $this->cdnAttributes
			+ $this->dataExportAttributes
			+ $this->smsProvidersAttributes;

		// All Attributes
		$this->attributes = $this->siteAttributes + $this->estateOnlyAttributes;

		// Company Attributes
		$this->companyAttributes = $this->generalAttributes + $this->addressAttributes;

		// Estate Attributes
		$this->estateAttributes = $this->generalAttributes + $this->estateOnlyAttributes;
	}

	/**
	 * Checks if the siteType is estate/company/site and the attribute is set for it's level
	 * @param $siteType
	 * @param $attributeName
	 */
	private function checkAttribute($siteType, $attributeName) {
		$this->createFlag = false;

		// If attribute is specific to estate
		if($siteType == 'estate' and array_key_exists($attributeName, $this->estateAttributes))
			$this->createFlag = true;
		elseif($siteType == 'company' and array_key_exists($attributeName, $this->companyAttributes))
			$this->createFlag = true;
		elseif($siteType == 'site' and array_key_exists($attributeName, $this->siteAttributes))
			$this->createFlag = true;
	}

	/**
	 * Sets up Adjets just onc, calling the API or sending an Email on fail
	 * @param $attributeName
	 * @param $id
	 */
	private function setAdjets($attributeName, $id) {
		//Checks if the adjets can be created
		if($attributeName == 'adjets' || $attributeName == 'gateway_id' || $attributeName == 'package_id' || $attributeName == 'venue_id') {
			//Check if we need to set them (so we won't do it multiple times)
			if($this->setAdjetsProperties) {
				//Check if we have gateway_id and package_id
				if(empty($this->requestData['gateway_id']) || empty($this->requestData['package_id']))
					$this->createFlag = false;
				else
					$this->adjets = [
						'setAdserverProperties'		=> $this->requestData['adjets'],
						'gateway_id' 				=> $this->requestData['gateway_id'],
						'package_id'				=> $this->requestData['package_id'],
						'venue_id'					=> $this->requestData['venue_id'] ?? ''
					];

				//Set up the Cache with the adjets for the site
				$adjets_val = $this->requestData['adjets'] == "true"? true: false;
				\Cache::forever('admin.site.adjets.'.$id, $adjets_val);

				//Call the API of the gateway to enable/disable adjets
				if($this->createFlag) {
					// Getting the gateway
					$gateway = GatewayModel::find($this->adjets['gateway_id']);

					// Trying to create an object from the gateway type
					$gatewayApiObject = Types::getGatewayApiObject($gateway->toArray());

					//if the gateway has an API class then it will call the getlogs function
					if(!is_null($gatewayApiObject)) {
						//Enable or disable adjets
						$response = $gatewayApiObject->toggleAdjetsStatus($adjets_val);

						if(!$response)
							dispatch(new EmailJob(null, 'applications@airangel.com', 'Failed activating/disabling adjets on site id: '.$id.' for server: '.config('app.env'), $response, null, []));
					}
					$this->setAdjetsProperties = false;
				}
			}
		}
	}

	private function setPrtg($attributeName, $id) {
		//Checks if the prtg sensors can be created
		if($attributeName == 'prtg_api_server' || $attributeName == 'prtg_api_username' || $attributeName == 'prtg_api_passhash') {
			//Check if we need to set them (so we won't do it multiple times)
			if($this->setPrtgProperties) {
				//Check if we have gateway_id and package_id
				if(empty($this->requestData['prtg_api_server']) || empty($this->requestData['prtg_api_username']) || empty($this->requestData['prtg_api_passhash']))
					$this->createFlag = false;
				else {

					$params = [
						'url' 		=> $this->requestData['prtg_api_server'],
						'username' 	=> $this->requestData['prtg_api_username'],
						'passhash'	=> $this->requestData['prtg_api_passhash'],
						'siteId'	=> $id,
						'content' 	=> 'sensor',
						'columns'	=> 'objid,sensor,device,group,status,parentid'
					];

					PrtgLogic::setUpPrtgServer($id, $this->requestData['prtg_api_server'], $params);

					//Don't add the site attributes here as it is already handled in the PRTG logic
					$this->createFlag = false;
					//Do this only once
					$this->setAdjetsProperties = false;
				}
			}
		}
	}

	/**
	 * Create the array that will be inserted into DB
	 *
	 * @param $id
	 * @param $attributeName
	 * @return mixed
	 */
	private function createInsertRow($id, $attributeName) {

		$row['ids'] = $id ;
		$row['name'] = $attributeName ;
		$row['value'] = $this->requestData[$attributeName] ;
		// Saving the value in the object portalAttributes
		$this->attributes[$attributeName] = $row['value'];

		switch (substr($attributeName, 0, 7)) {
			case 'campaig':
			case 'mailchi':
				$row['type'] = 'email_marketing' ;
				break;
			case 'pms':
			case 'paypal':
			case 'paypal_':
			case 'worldpa':
				$row['type'] = 'payment' ;
				break;
			default:
				$row['type'] = 'site' ;
		}

		$row['status'] 	= 'active' ;
		$row['created'] = \Carbon\Carbon::now();

		return $row;
	}

	/**
	 * Gets the actual array that will be inserted and checks if the attribute is excluded. If it is, it removes the old record if the value has been changed
	 * This is used for permissions (so we won't lose the old attributes if the site has been saved by an user that doesn't have all permissions)
	 *
	 * @param $data
	 * @param $id
	 */
	private function setExcludedSiteAttributes(&$data, $id ) {
		//Get the excluded attributes
		$excludedAttributes = SiteAttributeModel::where('ids', $id)->get()
			->pluck('value', 'name')
			->toArray();
		$delete = [];

		//Check if the new attribute is already saved and has been excluded from delete
		foreach($data as $key => $insert) {
			if(in_array($insert['name'], array_keys($excludedAttributes))) {
				//Check if the value is the same and stop it from inserting or remove the old value
				if($insert['value'] == $excludedAttributes[$insert['name']])
					unset($data[$key]);
				else
					$delete[] = $insert['name'];
			}
		}

		//Bulk remove all the site attributes
		if(!empty($delete))
			SiteAttributeModel::where('ids', $id)->whereIn('name', $delete)->delete();
	}
	/**
	 * Create Attributes for the new portal
	 * @param null $id
	 */
	private function createSiteAttributes($id = null) {
		$data = [];

		//Get the site that the user is Logged in
		if(is_null($id))
			$id = $this->modelObject['id'];

		$siteType = $this->requestData['sitetype'];
		// Cleaning Posted data for not completed data
		//$this->cleanPostedData();

		foreach ($this->attributes as $attributeName => $attributeValue) {
			// If there is no data in the form, skip!
			if(!isset($this->requestData[$attributeName]))
				continue;

			//set $this->createFlag for the attributes that are parsed
			$this->checkAttribute($siteType, $attributeName);

			if(!$this->createFlag)
				continue;

			//If AdJets attributes are set
			if(config('services.adjets.enabled'))
				$this->setAdjets($attributeName, $id);

			//Check for Prtg attribute
			$this->setPrtg($attributeName, $id);

			//Create data for insert
			$row = $this->createInsertRow($id, $attributeName);
			$data[] = $row;
		}

		//insert/update AdJets Attributes if there are set
		if( !empty($this->adjets) && $this->adjets['setAdserverProperties'] == 'true')
			Sites::setAdserverProperties($id, $this->adjets['gateway_id'], $this->adjets['package_id'], $this->adjets['venue_id']);

		//Set actual array that will be inserted and checks if the attribute is excluded. If it is, it removes the old record if the value has been changed
		$this->setExcludedSiteAttributes($data, $id);

		$siteAttribute = new SiteAttributeModel();
		$siteAttribute::insert($data);

		//Activate/Deactivate CDN for all portals of the edited site
		\App\Admin\Modules\Portals\Logic::toggleCdn($id);
	}

	/**
	 * Creating a new Site
	 */
	public function create()
	{
		// Removing the location from the site if it is an estate
		if(isset($this->requestData['sitetype']) and $this->requestData['sitetype'] == 'estate')
			$this->requestData['location'] = '';

		$this->requestData['status'] 	= 'active';
		$this->requestData['version'] 	= 3;
		// Creating the site
		parent::create();

		// Setting up the session to include the new site Id if needed
		if (session('admin.site.loggedin'))
			Sites::setupSession(session('admin.site.loggedin'), true);
		else
			Sites::setupSession(session('admin.user.site'), true);

		// Creating Site Attributes
		$this->createSiteAttributes();
	}

	/**
	 * Updating existing Site
	 * @param $id
	 */
	public function update($id)
	{
		// Removing the location from the site if it is an estate
		if(isset($this->requestData['sitetype']) and $this->requestData['sitetype'] == 'estate')
			$this->requestData['location'] = '';

		// Updating site
		parent::update($id);

		if(!\Request::ajax()) {
			// Delete existing Site Attributes and create new ones
			// Except type pms and if more types to exclude we can put them here in the array
			SiteAttributeModel::where('ids', $id)->whereNotIn('type', $this->dontDeleteTypes)->whereNotIn('name', $this->dontDeleteAttributes)->delete();
			$this->createSiteAttributes($id);
		}

		// Setting up the session to include the edited site Id if needed
		if (session('admin.site.loggedin'))
			Sites::setupSession(session('admin.site.loggedin'), true);
		else
			Sites::setupSession(session('admin.user.site'), true);
	}
}