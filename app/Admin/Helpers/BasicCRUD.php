<?php

namespace App\Admin\Helpers;

use App\Admin\Middleware\Access;
class BasicCRUD
{
    protected $rules;
    protected $modelName ;
    public	  $requestData;
    protected $successMsg;
    public 	  $modelObject;

    /**
     * Constructing the Model and the request data
     * BasicCRUD constructor.
     * @param $model
     * @param $connection
     */
    public function __construct($model, $connection = 'AirConnect')
    {
        $this->modelName = '\App\Models\\'.$connection.'\\'.$model;
        $this->requestData = \Request::all();
    }

	/**
	 * Saves the form either by creating a new record or updating existing record
	 * @param null $id
	 * @param null $status
	 * @param null|string $customRedirectUrl
	 * @return bool|\Illuminate\Http\RedirectResponse
	 */
    public function saveForm($id = null, $status = null, $customRedirectUrl = '')
	{
		//dump($id);
		//dd($status);
		//dd($this->requestData);
		if(\Gate::denies('access', Access::permissionFromPath(\Request::path() . '/edit')))
			$customRedirectUrl = \Request::path();

		// Checking the new-status from the AJAX call, if any
		if (isset($this->requestData["new-status"]))
			$status = $this->requestData["new-status"];

		// Ajax call, If status is sent as a parameter then add it to the $requestData array
		if (!is_null($status)) {
			// Sent via activate or delete button
			$this->requestData["status"] = $status;
			$this->update($id, $this->requestData);
			return 1;
		}

		$validator = \Validator::make($this->requestData, $this->rules);

		// If validation fails, return back and refill all fields
		if ($validator->fails())
			return \Redirect::back()
				->withErrors($validator)->withInput();

		// Create or Update
		if (is_null($id))
		{
			// We can now return a redirect if we need to
			$return = $this->create();
			if( $return instanceof \Illuminate\Http\RedirectResponse )
				return $return;

			$redirectUrl = '/' . \Request::path() . '/' . $this->modelObject['id']. '/edit';
		}
		else
		{
			$this->update($id);
			$redirectUrl = '/' . \Request::path() . '/edit';
		}

        Messages::create(Messages::SUCCESS_MSG, $this->successMsg);

		if(empty($customRedirectUrl))
			return \Redirect::to($redirectUrl)->with('edited', true);
		else
			return \Redirect::to($customRedirectUrl);
    }

    /**
     * Creates a new Object from the modelName
     */
    public function create()
    {
        $model = new $this->modelName;
        $this->modelObject = $model::create($this->requestData);
    }

    /**
     * Updates an existing Object
     * @param $id
     */
    public function update($id)
    {
        $model = new $this->modelName;
        $this->modelObject = $model::find($id);

        $this->modelObject->update($this->requestData);

    }

	/**
	 * Deletes the record
	 * Softdelete is the default
	 * @param $id
	 * @param bool $hard
	 * @return RedirectResponse|int
	 */
    public function delete($id, $hard = false)
    {
    	if(!$hard)
        	return $this->saveForm($id, 'deleted');
		else
		{
			$model = new $this->modelName;
			$this->modelObject = $model::find($id);

			$this->modelObject->delete();
			return 1;
		}
    }
}