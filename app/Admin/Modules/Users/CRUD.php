<?php

namespace App\Admin\Modules\Users;

use \App\Admin\Helpers\BasicCRUD;
use App\Admin\Helpers\Rules;
use App\Models\AirConnect\Admin as AdminModel;


/**
 * Class CRUD
 * @package App\Admin\Modules\Vouchers
 */
class CRUD extends BasicCRUD
{
    public $rules =
        [
            'username' 	=> 	Rules::REQUIRED,
			'site'		=>	Rules::REQUIRED,
			'role_id'	=>	Rules::REQUIRED,	// was adminId
			'password'	=>	Rules::REQUIRED,
			'language'	=>	Rules::REQUIRED,
			'timezone'	=>	Rules::REQUIRED
        ];



	/**
	 * Deleting existing Admin(soft)
	 * @param $id
	 * @param bool $hard
	 * @return \Illuminate\Http\RedirectResponse|int
	 */
    public function delete($id, $hard=false) {
		return parent::delete($id, $hard);
    }

	/**
	 * Creating a new Admin
	 */
	public function create() {

		//Set the Admin as "active"
		$this->requestData['status'] = 'active';
		//Set the unique rule for email
		$this->rules['username'] .= '|unique:airconnect.admin';
		//Set the creator of this Admin
		$this->requestData['creator'] = session('admin.user.username');
		//Hash the password
		$this->requestData['password'] = \Hash::make($this->requestData['password']);

		$validator = \Validator::make($this->requestData, $this->rules);

		// If validation fails, return back and refill all fields
		if ($validator->fails())
			return \Redirect::back()
				->withErrors($validator)->withInput();

		// Creating the Admin
		parent::create();
	}

	/**
	 * Updating existing Admin
	 * @param $id
	 */
	public function update($id) {

		//Get the admin to check if the password has been changed
		$admin = AdminModel::find($id);

		//Check if the password has been changed or unset it
		if (isset($this->requestData['password']) && substr($admin->password,0,7) !== $this->requestData['password'])
			$this->requestData['password'] = \Hash::make($this->requestData['password']);
		else
			unset($this->requestData['password']);

		parent::update($id);
	}

}