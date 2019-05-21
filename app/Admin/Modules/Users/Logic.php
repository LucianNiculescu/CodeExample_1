<?php


namespace App\Admin\Modules\Users;

use App\Models\AirangelTools\Gender;
use App\Models\AirangelTools\Processor;
use \App\Models\AirConnect\Admin as AdminModel;
use App\Admin\Helpers\Messages;
use \App\Admin\Helpers\Rules;
use App\Models\AirConnect\User;
use App\Models\AirConnect\UserAttribute;

class Logic
{
	// Setting session from the AdminModel Object sent from the AUTH::user() after logging in.
	public static function setSession($admin)
	{
		session([
			'admin.user.id' 			=> $admin->id,
			'admin.user.site' 			=> $admin->site,
			'admin.user.role' 			=> null , //added later in controller around line 65
			'admin.user.role_id' 		=> $admin->role_id ,	// was adminId
			'admin.user.template_id' 	=> $admin->template_id ,
			'admin.user.username' 		=> $admin->username ,
			'admin.user.email_reports' 	=> $admin->email_reports ,
			'admin.user.language' 		=> substr( $admin->language, 0, 2 ),
			'admin.user.timezone' 		=> $admin->timezone ,
			'admin.user.status' 		=> $admin->status
		]);
	}

	private $user 		 = null; 		// is an AdminModel object
	private $role 		 = null; 			// is the name of the role came from an array of role id and name like ['id' => 0 , 'role' => 'Dev']
	private $permissions = [];	// is an array of permissions
	public $error 		 = false;

	public function __construct($user)
	{
		$this->user = $user;
		self::setSession($user);

		//TODO: error handling
		//dd(AdminModel::where('id',$this->user->id)->with('roles')->get()->toArray());


		try{
			// using eager loading will load the roles and permissions from the AdminModel Object
			$this->role = AdminModel::where('id',$this->user->id)->with('roles')->get()->toArray()[0]['roles'][0]['role'];

			session(['admin.user.role' => $this->role]);

			$tempPermissions = AdminModel::where('id',$this->user->id)->with('permissions')->get()->toArray()[0]['permissions'];

			foreach($tempPermissions as $permission)
			{
				$this->permissions[] = $permission['permission'];
			}
		}catch (\Exception $e)
		{
			//TODO: check Message details
			Messages::create(Messages::ERROR_MSG, session('admin.user.username') .' has failed to setup session info for roles and permissions !');
			$this->error = true;
		}
	}

	/**
	 * getting the user's role
	 * @return string
	 */
	public function getRole()
	{
		return $this->role;
	}

	/**
	 *  getting an array of user's permissions
	 * @return array
	 */
	public function getPermissions()
	{
		return $this->permissions;
	}

	/**
	 * getting the AdminModel user Object which includes 'site', 'role_id', 'template_id',  'username',  'password', 'email_reports', 'language', 'timezone', 'status'
	 * @return object
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * DEPRECATED METHOD (NOW WE ARE USING THE CRUD SYSTEM)
	 * Saves User either from a create new User Form or Edit  Form
	 * @param null $id
	 * @param null $status
	 * @return \Illuminate\Http\RedirectResponse
     */
    public static function saveForm($id = null, $status = null)
    {
        // Return path
        $modulePath = '/' . \Request::path();
        // Request data
        $requestData = \Request::all();
        //dd($requestData);


        // Save rules for the form
        if (!\Request::ajax()) {
            $rules = [
                'username'		=>	Rules::REQUIRED . (is_null($id)? '|unique:airconnect.admin':''),
                'site'			=>	Rules::REQUIRED,
                'role_id'		=>	Rules::REQUIRED,	// was adminId
                'password'		=>	Rules::REQUIRED,
                'language'		=>	Rules::REQUIRED,
                'timezone'		=>	Rules::REQUIRED,

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

        if (is_null($id))
        {// Create
            $requestData["creator"] = session('admin.user.username');
            $requestData["password"] = \Hash::make($requestData["password"]);

            AdminModel::create($requestData);
        }
        else
        {// Update
            $user = AdminModel::find($id);

            if(!\Request::ajax())
                if (substr($user->password,0,7) != $requestData["password"])
                    $requestData["password"] = \Hash::make($requestData["password"]);
            else
                array_forget($requestData,'password');


            $user->update($requestData);
        }
        // Ajax returns 1 if successful
        if(\Request::ajax())
            return 1;
        // Tell the user
        Messages::create(Messages::SUCCESS_MSG, trans('admin.user-saved'));
        // Redirect back to the datatable
        return \Redirect::to($modulePath);
    }

}