<?php

namespace App\Admin\Modules\Gateways;

use \App\Admin\Helpers\BasicCRUD;
use App\Models\AirConnect\GatewayAttribute as GatewayAttributeModel;
use App\Models\AirHealth\Hardware as HardwareModel;
use App\Models\AirConnect\Gateway as GatewayModel;
use App\Admin\Helpers\Rules;

class CRUD extends BasicCRUD
{
    public $rules =
        [
            'type'			=>	Rules::REQUIRED,
            'name'			=>	Rules::REQUIRED_NAME,
			'mac'			=> 	Rules::MAC,
			'description'	=>	'max:50',
			'notes'			=>	'max:255',
            'nasid'			=>	Rules::REQUIRED,
            'site'			=>	Rules::REQUIRED,
        ];

    public static $customActions = [ 'edit', 'delete', 'view'];

    public $attributes =
		[
			'change_of_auth' 		=> '',
			'coa_port'				=> '',
			'disable_dynamic_ip'	=> ''
		];
    /**
     * CRUD constructor.
     * Constructing the needed Model
     * Setting the site to be loggedin site
     * And filling the all portal attribute array
     * @param $model
     */
    public function __construct($model)
    {
        parent::__construct($model);

        $this->successMsg = trans('admin.gateway-saved');
    }

    /**
     * Creating a new Gateway and hardware
     * If will create them if there is no hardware with the same mac, or if there is hardware with same mac and site is 0
     * otherwise it will error saying hardware already found
     */
    public function create()
    {
        // Adding extra rule so the mac won't be reused
        $this->rules[] = Rules::MAC ; //. '|unique:airconnect.gateway';
        $this->requestData['status'] = 'active';

        // Checking if there is a hardware with the same mac or not
        $hardware    = HardwareModel::where('mac',$this->requestData['mac'])->first();

        if (is_null($hardware))
        {// Creating the hardware with data sent in the form
            $hardware = new HardwareModel();
            $hardware->create($this->requestData + HardwareModel::$hardwareDefaults); // adding default values for the hardware table

        }
        elseif($hardware->site == 0)    // If hardware is there and the site is 0, then update the hardware
            $hardware->update($this->requestData);
        else
            abort('501', trans('error.hardware-found'));

        // Creating the gateway
        parent::create();

		//Create gateway attributes
		$this->createGatewayAttributes();

    }

    /**
     * Updating existing Gateway and hardware
     * It will check if there is no hardware then it should create it, otherwise it will update both gateway and hardware
     * @param $id
     */
    public function update($id)
    {
        // Updating gateway
        parent::update($id);

		//Create gateway attributes
		$this->createGatewayAttributes($id);

        // Getting the hardware with same mac
        $hardware    = HardwareModel::where('mac',$this->modelObject->mac)->first();

        // If there was no hardware then create it otherwise update it
        if (is_null($hardware))
        {
            // Create the hardware from the form if it is not Ajax because Ajax calls are sending few information only
            if(!\Request::ajax())
            {
                $hardware = new HardwareModel();
                $hardware->create($this->requestData + HardwareModel::$hardwareDefaults);
            }
        }
        else
            $hardware->update($this->requestData);


    }


    /**
     * Deleting existing Gateway(soft) and hardware
     * @param $id
     * @param $hard
	 * @return int
     */
    public function delete($id, $hard = false)
    {
		parent::delete($id, $hard);

        // Getting the hardware with same mac
        $hardware    = HardwareModel::where('mac',$this->modelObject->mac)->first();

        // If there was a hardware then hard delete it
        if (!is_null($hardware))
            $hardware->delete();

		return 1;
    }

    /**
     * Reading the gateway and and returning what in hardware table to fill the edit form
     * @param $id
     * @return mixed
     */
    public static function getGateway($id)
    {
		return GatewayModel::find($id);

		/* DISABLED AT THE MOMENT, WE USE THE GATEWAY FROM AIRCONNECT
        $gateway    = GatewayModel::find($id);
        $hardware   = HardwareModel::where('mac', $gateway->mac)->first();

        // If for any reason there no hardware with the same mac, return gateway
        if(is_null($hardware))
            return $gateway;

		 return $hardware;
        */


    }

	/**
	 * TODO: NOT USED ?
	 * Gets all Gateway IDs that user is authorized to edit
	 * @param $systemPage
	 * @return mixed
	 */
	public static function getGatewaysIds($systemPage)
	{
		$gateways = GatewayModel::select('id');

		if($systemPage)
			$gateways = $gateways->whereIn('site', session('admin.site.estate'));

		$gateways = $gateways->get()
			->keyBy('id')
			->toArray();

		return $gateways;
	}

	/**
	 * Create Attributes for the new gateway
	 * @param null $id
	 */
	private function createGatewayAttributes($id = null) {
		$data = [];

		//Get the site that the user is Logged in
		if(is_null($id))
			$id = $this->modelObject['id'];

		//Delete the old attributes of this gateway (just the ones for V3)
		GatewayAttributeModel::where(['ids' => $id, 'type' => 'gatewayV3'])->delete();

		//Go through attributes and set the for insert
		foreach ($this->attributes as $attributeName => $attributeValue) {
			// If there is no data in the form, skip!
			if(!isset($this->requestData[$attributeName]))
				continue;

			//Create data for insert
			$row = $this->createInsertRow($id, $attributeName);
			$data[] = $row;
		}

		$siteAttribute = new GatewayAttributeModel();
		$siteAttribute::insert($data);
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
		// Saving the value in the object
		$this->attributes[$attributeName] = $row['value'];
		$row['type'] = 'gatewayV3';
		$row['status'] 	= 'active' ;
		$row['created'] = \Carbon\Carbon::now();

		return $row;
	}
}