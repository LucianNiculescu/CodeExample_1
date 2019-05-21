<?php
namespace App\Admin\Modules\Forms;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Gets known question
     */
    public function getQuestion($id)
    {
        $data = CRUD::getQuestion($id);
        return $data;
    }

    /**
     * Display a listing of the Forms
     */
    public function index()
    {
        $data = SetupViewData::index();
        return view('admin.modules.forms.client-side-index', $data);
    }

    /**
     * Show the form for creating a new record.
     */
    public function create()
    {
        $data = SetupViewData::create();
        // Open the create view
        return view('admin.modules.forms.form' , $data);

    }

    /**
     * Show the form for editing the specified forms.
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = SetupViewData::edit($id);
        // Show the edit form and pass the data
        return view('admin.modules.forms.form', $data);
    }

    /**
	 * Display the specified forms
	 * @param $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function show($id)
	{
        $modulePath = '/' . \Request::path();
        $length = strpos($modulePath, 'forms') + 5;
        $modulePath = substr($modulePath, 0 , $length);
        //dd($modulePath);
        return \Redirect::to($modulePath);
	}

    /**
	 * @param  int  $id
	 * @return \App\Admin\Helpers\Logic|\Illuminate\Http\RedirectResponse
     */
	public function destroy($id)
	{
        $questionCRUD = new CRUD('Form');
        return $questionCRUD->delete($id);
	}

    /**
	 * Save form calls the Logic saveForm method
	 */
	public function store()
	{
		$questionCRUD = new CRUD('Form');
		return $questionCRUD->saveForm();
	}

    /**
	 * Update the specified forms
	 */
	public function update($id)
	{
		$questionCRUD = new CRUD('Form');
		return $questionCRUD->saveForm($id);
	}
}