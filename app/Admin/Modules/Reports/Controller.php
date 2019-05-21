<?php
namespace App\Admin\Modules\Reports;

use Illuminate\Routing\Controller as BaseController;
use App\Admin\Modules\Reports\Logic as ReportsLogic;
use Illuminate\Http\Request;
use App\Models\AirConnect\Gateway as GatewayModel;


class Controller extends BaseController
{
	/**
	 * Controller constructor defining middleware to require a logged in site on create
	 */
	public function __construct()
	{
		$this->middleware('require_site', ['only' => ['CsvReports']]);
	}

	/**
	 * Display the CSVReports Widgets page
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function CsvReports()
	{
		$types = CsvReports::$types;
		$data = [
		    'types'         => $types,
			'title' 		=> trans('admin.csv-reports'),
			'description'	=> trans('admin.csv-description'),
			'hideCreate'	=> true,
			'titleClass'	=> 'title-widget',
			'helpPage' 		=> 'reports-csv|index',
		];
		return view('admin.modules.reports.index', $data);
	}

    /**
     * Create the CSV file, trigger the queue system and redirect (having a Message already in the session
	 * @param Request $request
	 * @return mixed
     */
    public function generateCsv(Request $request)
    {
        ReportsLogic::generateCSV($request);
        return \Redirect::to('reports/csv');
    }

	/**
	 * @param $title Title of the dashboard(translated)
	 * @param $help help page for the dashboard (to be used with trans)
	 * @param $reportTypeCss tells CSS which type of dashboard is being displayed
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function displayDashboard($title, $help, $reportTypeCss)
	{
		$data = [
			'title' 		=> $title,
			'description'	=> '',
			'gateways' 		=> GatewayModel::getAllGatewayBySite(session('admin.site.loggedin')),
			'includeMapJs'	=> true,
			'helpPage' 		=> $help,
			'reportTypeCss'	=> $reportTypeCss,
		];
		return view('admin.modules.reports.dashboard', $data);
	}


	/**
	 * Display the GuestReports Widgets page
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function guestDashboard()
	{
		return $this->displayDashboard(trans('admin.guest-reports'), 'reports-guest|index', 'guest-reports');
	}

	/**
	 * Display the financialReports Widgets page
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function financialDashboard()
	{
		return $this->displayDashboard(trans('admin.financial-reports'), 'reports-financial|index', 'financial-reports');
	}

	/**
	 * Display the technologyReports Widgets page
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function technologyDashboard()
	{
		return $this->displayDashboard(trans('admin.technology-reports'), 'reports-technology|index', 'technology-reports');
	}


	/**
	 * Get data to generate report
	 * @param $widget
	 * @return mixed
	 */
	public function getReportData( $widget )
	{
		$request = \Request::all();
		$period = $request['period'] ?? null;
		$from 	= $request['from'] 	 ?? null;
		$to 	= $request['to'] 	 ?? null;
		$mac 	= $request['mac'] 	 ?? null;
		$route 	= $request['route']	 ?? null;
		$method = $request['method'] ?? null;
		$id 	= $request['id']	 ?? null;

		//In case we need to call different methods
		if(!empty($method))
			return call_user_func($method, $id);

		return ReportsLogic::getReportData($widget, $period, $from, $to, $mac, $route);
	}

}