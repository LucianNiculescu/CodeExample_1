<?php
namespace App\Admin\Modules\Roles;

use View;
use Illuminate\Routing\Controller as BaseController;
use App\Admin\Modules\Roles\Logic as Roles;
/**
 * Class Controller
 * @package App\Admin
 */
class Controller extends BaseController
{

	/**
	 * Display the specified role
	 * @param $id
	 * @return array
	 */
	public function show($id)
	{
		if(!\Request::ajax())
		{
			$modulePath = '/' . \Request::path();
			$length = strpos($modulePath, 'roles-and-permissions') + 21;
			$modulePath = substr($modulePath, 0 , $length);
			return \Redirect::to($modulePath);
		}
		else
		{
			if(strpos(\Request::path(), 'system') === false)
				$systemPage = false;
			else
				$systemPage = true;

			$managedRoles = Roles::getManagedRoles(session('admin.user.role_id'), $systemPage);

			if(!in_array($id, $managedRoles))
				abort('401', trans('error.not-authorized'));

			$data['categoryPermissions'] = Roles::listPermissions(session('admin.user.role_id'), $id, $systemPage);
			$view = view('admin.modules.roles.show', $data);
			return $view;
		}
	}

	/**
	 * Display a listing of the Roles in a client side datatable
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
    public function index()
    {
		if(strpos(\Request::path(), 'system') === false)
			$systemPage = false;
		else
			$systemPage = true;

		$data = SetupViewData::index($systemPage);

        return view('admin.modules.roles.client-side-index', $data);
    }

    /**
     * Show the form for creating a new Role.
     */
    public function create()
    {
		if(strpos(\Request::path(), 'system') === false)
			$systemPage = false;
		else
			$systemPage = true;

		$data = SetupViewData::create($systemPage);

        // Open the form view
        return view('admin.modules.roles.form' , $data);

    }


    /**
     * Show the form for editing the specified resource.
     * @param  int $id
     * @return View
     */
    public function edit($id)
    {
		if(strpos(\Request::path(), 'system') === false)
			$systemPage = false;
		else
			$systemPage = true;

		$managedRoles = Roles::getManagedRoles(session('admin.user.role_id'), $systemPage);

		if(!in_array($id, $managedRoles))
			abort('401', trans('error.not-authorized'));

		$data = SetupViewData::edit($id, $systemPage);



        // Show the edit form and pass the data
        return view('admin.modules.roles.form', $data);

    }


    /**
	 * create a role with the posted data from the form
	 */
	public function store()
	{
		$roleCRUD = new CRUD('Role');
		return $roleCRUD->saveForm();
	}


    /**
	 * Update the specified Role
	 */
	public function update($id)
	{
		$roleCRUD = new CRUD('Role');
		return $roleCRUD->saveForm($id);
	}
}