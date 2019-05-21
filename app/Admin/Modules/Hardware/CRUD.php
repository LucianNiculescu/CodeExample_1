<?php

namespace App\Admin\Modules\Hardware;

use \App\Admin\Helpers\BasicCRUD;
use App\Models\AirHealth\Hardware as HardwareModel;
use App\Admin\Helpers\Rules;

class CRUD extends BasicCRUD
{
    public $rules =
        [
            'type'			=>	Rules::REQUIRED,
            'location'		=>	Rules::REQUIRED,
            'name'			=>	Rules::REQUIRED_NAME,
			'description'	=>	'max:50',
			'notes'			=>	'max:255',
            'site'			=>	Rules::REQUIRED,
			'mac'			=> Rules::MAC
        ];


    public static $customActions = ['edit', 'delete', 'view'];

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
		$this->modelName = '\App\Models\AirHealth\\'.$model;
        $this->successMsg = trans('admin.hardware-saved');
    }

    /**
     * Creating a new hardware
     */
    public function create()
    {

		$this->requestData = $this->requestData + HardwareModel::$hardwareDefaults;
        $this->requestData['status'] = 'active';
        // Creating the gateway
        parent::create();
    }

	/**
	 * Deleting existing hardware(hard)
	 * @param $id
	 * @return \Illuminate\Http\RedirectResponse|int
	 */
    public function delete($id, $hard = false)
    {
		return parent::delete($id, true);
    }


	/**
	 * Overwriting saveForm to add unique validation to the user
	 * @param null $id
	 * @param null $status
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function saveForm($id = null, $status = null, $customRedirectUrl = '')
	{
		if(is_null($id))
			// Adding extra rule so the mac won't be reused
			$this->rules['mac'] = Rules::MAC . '|unique:airhealth.hardware';

		return parent::saveForm($id, $status);
	}

    /**
	 * TODO: is this used?
     * Get the hardware to check if the user is allowed to edit it or not
     * @param $id
     * @return mixed
     */
    public static function getHardware($id)
    {
		$hardware = HardwareModel::whereIn('site', session('admin.site.estate'))
			->where('id', $id)
			->first();

        return $hardware;
    }

	/**
	 * TODO: is this used? don't think so
	 * Gets all Hardware IDs that user is authorized to edit
	 * @param $count
	 * @return mixed
	 */
	public static function getHardwareIds($count = false)
	{
		$hardware = HardwareModel::whereIn('site', session('admin.site.estate'));

		if($count)
			$hardware = $hardware->count();
		else
			$hardware = $hardware->select('id')
				->get()
				->keyBy('id')
				->toArray();

		return $hardware;
	}
}