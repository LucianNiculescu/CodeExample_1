<?php
namespace App\Admin\Modules\Hardware;

use Illuminate\Routing\Controller as BaseController;
use App\Models\HHE\Monitoring as MonitoringModel;

class Controller extends BaseController
{
     /**
     * Display a listing of the Hardware
     */
    public function index()
    {
		// If Ajax then it is a widget
		if(\Request::ajax())
		{
			$data = SetupViewData::serverSideDatatable(true);
			return view('admin.modules.hardware.server-side-widget', $data );
		}
		else
		{
			$data = SetupViewData::serverSideDatatable();
			return view('admin.modules.hardware.server-side-index', $data );
		}
    }

    /**
	 * Show the form to create new hardware
	 */
	public function create()
	{
        $data = SetupViewData::create();
		return view('admin.modules.hardware.form' , $data);
	}

    /**
	 * Show the form for editing the specified hardware
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit($id)
	{
        $data = SetupViewData::edit($id);
		return view('admin.modules.hardware.form', $data);
	}


    /**
     * Display the specified hardware
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
		if(!\Request::ajax())
		{
			$modulePath = '/' . \Request::path();
			$length = strpos($modulePath, 'hardware') + 8;
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
     * softDelete method in a hardware changes the status to deleted
     * @param  int  $id
     * @return Logic|\Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $hardwareCRUD = new CRUD('Hardware');
        return $hardwareCRUD->delete($id);
    }

    /**
	 * Save form calls the Logic saveForm method
	 */
	public function store()
	{
        $hardwareCRUD = new CRUD('Hardware');
        return $hardwareCRUD->saveForm();
	}

    /**
	 * Update the specified Hardware
	 */
	public function update($id)
	{
		$hardwareCRUD = new CRUD('Hardware');
        return $hardwareCRUD->saveForm($id);
	}

    /**
     * Setting up the datatable to send it via JSON
     * @return mixed
     */
	public function getHardwareDatatable()
	{
		// If it is coming from the dashboard then it is the widget that will include the gateways
		if(\Request::ajax() and strpos(\URL::previous(), 'dashboard') !== false)
			return self::getHardwareGatewayDatatable();

		// Else it is the normal hardware datatable without the gateways
		$query = Datatable::getHardware();
		// Return the Datatable json
		return Datatable::makeTable($query);
	}

    /**
     * Setting up the datatable to send it via JSON
     * @return mixed
     */
	public function getHardwareGatewayDatatable()
	{
		$query = Datatable::getHardware(false, true);
		// Return the Datatable json
		return Datatable::makeTable($query);
	}

	public function showApListWidget()
	{
		$detailsColumns = [
			'details'	=> [
				'model'			=> '',
				'channel2'		=> '',
				'channel5'		=> '',
				'mesh'			=> '\App\Admin\Helpers\Datatables\MeshColumn',
			]
		];
		$data = [
			'tableId'	=> 'hhe',
			'rows' 		=> Datatable::getHheMonitoring(),
			'columns' 	=> [
				'status'		=> '\App\Admin\Helpers\Datatables\HheStatusColumn',
				'devicename'	=> '',
				'details'		=> '',
				'last_seen'		=> '\App\Admin\Helpers\Datatables\DateColumn',
				'uptime'		=> '\App\Admin\Helpers\Datatables\DurationColumn',
				'clients'		=> '',
				'uptime1'	=> '\App\Admin\Helpers\Datatables\DurationInSecondsColumn'
			],
			'customActions'		=> ['delete'],
			'hhe'				=> true,
			'detailsColumns'	=> $detailsColumns,
			'route'				=> 'widgets/ap-list',
			'showActions'		=>  ( \Gate::allows('access', 'widgets.ap-list.delete') )
		];
		return view('admin.modules.hardware.ap-list-client-side-widget', $data );
	}

	public function deleteHhe($serial)
	{
		$hhe = MonitoringModel::where(['serial'=> $serial])->first();
		if(!is_null($hhe))
		{
			$hhe->delete();
			return 1;
		}
		else
		{
			return "AP not found";
		}
	}
}