<?php

namespace App\Admin\Modules\Users;

use Auth;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Input;
use \App\Admin\Modules\Users\Logic as User;
use \App\Admin\Helpers\Messages;
use App\Models\AirConnect\Site as SiteModel;


/**
 * Class Controller
 * @package App\Admin\Modules\Users
 */
class Controller extends BaseController
{
    /**
     * Display a listing of the users
     */
    public function index() {

    	$data = SetupViewData::getTableData();
        return view('admin.modules.users.index', $data);
    }

    /**
     * Show the form for creating a new record.
     */
    public function create() {

    	$data = SetupViewData::create();
        return view('admin.modules.users.form' , $data);
    }


    /**
     * Show the form for editing the specified Admin.
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id) {

    	$data = SetupViewData::edit($id);
        return view('admin.modules.users.form', $data);
    }

    /**
     * Display the specified users
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id) {
        $modulePath = '/' . \Request::path();
        $length = strpos($modulePath, 'users') + 5;
        $modulePath = substr($modulePath, 0 , $length);
        return \Redirect::to($modulePath);
    }

	/**
	 * softDelete the Admin (set status to 'deleted')
	 * @param  int  $id
	 * @return Logic|\Illuminate\Http\RedirectResponse
	 */
	public function destroy($id) {

		$adminCRUD = new CRUD('Admin');
		return $adminCRUD->delete($id);
	}

	/**
	 * Create the Admin
	 */
	public function store() {

		$adminCRUD = new CRUD('Admin');
		return $adminCRUD->saveForm();
	}

	/**
	 * Update the specified Admin
	 * @param $id
	 * @return mixed
	 */
	public function update($id) {

		$adminCRUD = new CRUD('Admin');
		return $adminCRUD->saveForm($id);
	}

	public function getSystemUsersDatatable()
	{
		return $this->getUsersDatatable(true);
	}

	/**
	 * this is called to get the Json object back to the estate route
	 * @param $systemPage bool
	 * @return mixed
	 */
	public function getUsersDatatable($systemPage = false)
	{
		return Datatable::setupTable($systemPage);
	}

    /**
	 * Todo: proper error handling - ???
	 */
	public function getLogin()
	{
		// if the user is already logged in they will be routed to estate page
		if (Auth::check()) {
			return redirect()->route('estate');
		}
		// otherwise return to login page
		return view('admin.login');
	}

	public function postLogin()
	{

		// post method will submit the login credentials through Auth
		// if login succeeds it will go to estate if failed it will stay in login page with an error
		$loginData = \Request::all();

		// if failed to login reroute user to login page
		if (!Auth::attempt(
			['username' 	=> $loginData['username'],
				'password' 	=> $loginData['password'],
				'status'	=> 'active'
			],false))
		{
			Messages::create(Messages::ERROR_MSG, trans('error.user-login-failed'));

			return redirect()->route('login')
				->withErrors(trans('error.wrong-login-credentials'))
				->withInput(Input::except('password'));
		}

		$userSite = SiteModel::where(['status' => 'active', 'id' => Auth::user()->site])->first();

		if(is_null($userSite))
		{
			Messages::create(Messages::ERROR_MSG, trans('error.no-site'));
			$this->logout();
			return redirect()->route('login')->withErrors(trans('error.no-site'));
		}
		else
		{
			$user = new User(Auth::user());

			if ($user->error)
				return redirect()->route('logout');

			Messages::create(Messages::SUCCESS_MSG, session('admin.user.username') .' has successfully logged in!!!', false);
			return redirect()->route('estate');
		}
	}

	/**
	 * Logout uses Auth to logout the Admin User
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function logout()
	{
		// logging out and resetting the admin session
		Auth::logout();
		session(['admin' => null]);
		return redirect('/login');
	}

}
