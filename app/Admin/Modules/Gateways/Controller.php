<?php
namespace App\Admin\Modules\Gateways;

use Illuminate\Routing\Controller as BaseController;
use App\Admin\Modules\Gateways\Types\Logic as Types;

use Gate;

class Controller extends BaseController
{
	/**
	 * Controller constructor defining middleware to require a logged in site on create
	 */
	public function __construct()
	{
		$this->middleware('require_site', ['only' => ['create']]);
	}

	/**
     * Display a listing of the Gateways
     */
    public function index()
    {
        if(\Request::path()=='gateways')
        {
            $data = SetupViewData::serverSideDatatable(false);
            return view('admin.modules.gateways.server-side-index', $data );
        }
        else
        {
            $data = SetupViewData::clientSideDatatable(true);
            return view('admin.modules.gateways.client-side-index', $data );
        }
    }

    /**
	 * Shows the create new Gateway form
	 */
	public function create()
	{
		$data = SetupViewData::create();
		return view('admin.modules.gateways.form' , $data);
	}

    /**
	 * Show the form for editing the specified Gateway
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit($id)
	{
        $data = SetupViewData::edit($id);
		return view('admin.modules.gateways.form', $data);
	}


    /**
     * Display the specified Gateway
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
		if(!\Request::ajax())
		{
			$modulePath = '/' . \Request::path();
			$length = strpos($modulePath, 'gateways') + 8;
			$modulePath = substr($modulePath, 0 , $length);
			return \Redirect::to($modulePath);
		}
		else
		{
			$data['showDetails'] = Logic::getDetails($id);
			$view = view('admin.modules.show-details', $data);
			return $view;
		}
    }


    /**
     * softDelete method in a Gateway changes the status to deleted
     * @param  int  $id
     * @return int
	 */
    public function destroy($id)
    {
        $gatewayCRUD = new CRUD('Gateway');
        return $gatewayCRUD->delete($id);
    }

    /**
	 * Save form calls the Logic saveForm method
	 */
	public function store()
	{
        $gatewayCRUD = new CRUD('Gateway');
        return $gatewayCRUD->saveForm();
	}

    /**
	 * Update the specified Gateway
	 */
	public function update($id)
	{
        $gatewayCRUD = new CRUD('Gateway');
        return $gatewayCRUD->saveForm($id);
	}

	/**
	 * this is called to get the Json object back to the estate route
	 * @return mixed
	 */
	public function getSystemGatewaysDatatable()
	{
		return $this->getGatewaysDatatable(true);
	}

    /**
     * Setting up the datatable to send it via JSON
     * @param bool $systemPage
     * @return mixed
     */
	public function getGatewaysDatatable($systemPage = false)
	{
		$query = Datatable::getGateways(false, $systemPage); // $clientSide = false;
		// Return the Datatable json
		return Datatable::makeTable($query, $systemPage);
	}

	/**
	 * Rebooting the gateway using AJAX call
	 * @return string
	 */
	public function rebootGateway()
	{
		return Types::rebootGateway();
	}

	/**
	 * Calling the AAA functionality
	 * @return string
	 */
	public function aaaGateway()
	{
		return Types::aaaGateway();
	}

	/**
	 * Returns a list of gateways based on a given packageId
	 * @param $packageId
	 * @return mixed
	 */
	public function getGatewaysByPackage($packageId) {
		return Logic::getGatewaysByPackage($packageId);
	}
}