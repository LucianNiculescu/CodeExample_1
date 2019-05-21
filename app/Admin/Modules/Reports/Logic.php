<?php
namespace App\Admin\Modules\Reports;

use \App\Models\Reports\DailySummaries as DailySummariesModel;
use \App\Models\Reports\HourlySummaries as HourlySummariesModel;
use \App\Models\Reports\HourlyHardwareHealth as HourlyHardwareHealthModel;
use \App\Models\Reports\TransactionSummaries as TransactionSummariesModel;
use \App\Models\Reports\DashboardStats as DashboardStatsModel;
use App\Models\AirHealth\Bandwidth as BandwidthModel;
use Illuminate\Http\Request;
use App\Jobs\CsvReportJob as CsvReportJob;
use App\Admin\Helpers\Messages;


class Logic
{
	// The login types are used by two widgets and a CSV report so we define them in a single place, here.
	const loginTypes = [
		'all-guests'	=>	[
			'db_column'	=> 'registered_users',
			'icon'		=> 'fa-users'
			],
		'email'			=>	[
			'db_column'	=> 'reg_users_airpass',
			'icon'		=> 'fa-envelope'
			],
		'facebook'		=>	[
			'db_column'	=> 'reg_users_facebook',
			'icon'		=> 'fa-facebook'
			],
		'twitter'		=>	[
			'db_column'	=> 'reg_users_twitter',
			'icon'		=> 'fa-twitter'
			],
		'linkedin'		=>	[
			'db_column'	=> 'reg_users_linkedin',
			'icon'		=> 'fa-linkedin'
			],
		'google'		=>	[
			'db_column'	=> 'reg_users_google',
			'icon'		=> 'fa-google-plus'
			],
		'voucher'		=> [
			'db_column'	=> 'reg_users_voucher',
			'icon'		=> 'fa-ticket'
			],
		'gha'			=> [
			'db_column'	=> 'reg_users_gha',
			'icon'		=> 'fa-glide-g'
			],
		'pms-short'		=> [
			'db_column'	=> 'reg_users_pms',
			'icon'		=> 'fa-hotel'
			],
		'voyat'			=> [
			'db_column'	=> 'reg_users_voyat',
			'icon'		=> 'fa-vimeo'
			],
		'quick_login'	=> [
			'db_column'	=> 'reg_users_quick_login',
			'icon'		=> 'fa-flash'
			],
		'live'			=> [
			'db_column'	=> 'reg_users_live',
			'icon'		=> 'fa-windows'
			],
		'whitelist'		=> [
			'db_column'	=> 'reg_users_whitelist',
			'icon'		=> 'fa-sign-in'
			],
		'other'		=> [
			'db_column'	=> 'reg_users_other',
			'icon'		=> 'fa-question'
		],
		];


	// Array to link widget title with the function to get the data
	private static $widgetFunctionsArray = [
		'dwell-time' 					=> Guest::class 		. '::getDwellTimeData',
		'gender' 						=> Guest::class 		. '::getGenderData',
		'registered-users'				=> Guest::class 		. '::getRegisteredUsersData',
		'login-types'					=> Guest::class 		. '::getLoginTypesData',
		'accumulated-guests'			=> Guest::class 		. '::getAccumulatedGuestsData',
		'new-guests'					=> Guest::class 		. '::getNewGuestsData',
		'demographics'					=> Guest::class 		. '::getDemographicsData',
        'logins-in-last-n'				=> Guest::class 		. '::getLoginsInLastNData',
		'average-gateway-latency'		=> Technology::class 	. '::getAverageGatewayLatencyData',
		'highest-gateway-latency'		=> Technology::class 	. '::getHighestGatewayLatencyData',
		'average-traffic'				=> Technology::class 	. '::getAverageTrafficData',
		'os-usage'						=> Technology::class 	. '::getOperatingSystemUsageData',
		'os-trends'						=> Technology::class 	. '::getOperatingSystemTrendsData',
		'browser-trends'				=> Technology::class 	. '::getBrowserTrendsData',
		'browser-usage'					=> Technology::class 	. '::getBrowserUsageData',
		'data-transferred'				=> Technology::class 	. '::getDataTransferredData',
		'net-packages'					=> Financial::class 	. '::getNetPackagesData',
		'most-used-package'				=> Financial::class 	. '::getMostUsedPackageData',
		'net-income'					=> Financial::class 	. '::getNetIncomeData',
		'average-net-income'			=> Financial::class 	. '::getAverageNetIncomeData',
		'cumulative-net-income'			=> Financial::class 	. '::getCumulativeNetIncomeData',
		'daily-cashflow'				=> Financial::class 	. '::getDailyCashflowData',
		'package-sales-income'			=> Financial::class 	. '::getPackageSalesIncomeData',
		'gateway-speed'					=> Dashboard::class 	. '::getAverageSpeedData',
		'gateway-logs'					=> Dashboard::class 	. '::getGatewayLogs',
		'gateway-control'				=> Dashboard::class 	. '::getGatewayControlData',

	];

	// Putting widgets that needs data from the hourly_summaries in the $hourlyWidgets array
	private static $hourlyWidgets = ['dwell-time', 'registered-users', 'accumulated-guests', 'new-guests'];

	// Putting the widgets that needs data from the hourlyhardwareHealth in the $hardwareHealthWidgets array
	private static $hardwareHealthWidgets = ['average-gateway-latency', 'highest-gateway-latency', 'average-speed'	];

	// Putting the widgets that needs data from the transaction_summaries in the $transactionsWidgets array
	private static $transactionsWidgets = ['net-packages', 'most-used-package', 'net-income', 'average-net-income', 'cumulative-net-income', 'daily-cashflow', 'package-sales-income'];

	private static $bandwidthWidgets = ['gateway-speed'];
	private static $nullTableWidgets = ['gateway-logs', 'gateway-control'];

	/**
	 * Getting the DailySummaries Data
	 * @param $widget
	 * @param $period
	 * @param $from
	 * @param $to
	 * @param bool $prevFlag
	 * @return mixed
	 */
	public static function getReportData($widget, $period, $from, $to, $mac, $route, $prevFlag = false)
	{
		// Most widgets uses daily_summaries Table
		$tableName = 'daily_summaries';

		if (in_array($widget, self::$hourlyWidgets))
			$tableName = 'hourly_summaries';
		else if (in_array($widget, self::$hardwareHealthWidgets))
			$tableName = 'hourly_hardware_health';
		else if (in_array($widget, self::$transactionsWidgets))
			$tableName = 'transaction_summaries';
		else if (in_array($widget, self::$bandwidthWidgets))
			$tableName = 'bandwidth';
		else if (in_array($widget, self::$nullTableWidgets))
			$tableName = null;

		// Setting up the [$from, $to] array
		$fromTo = self::setupPeriod($period, $from, $to, $prevFlag);

		// Running the Daily Summaries Query giving the widget name and from to array
		return self::runWidgetFunction($widget,  $fromTo, $period, $mac, $route, $tableName);
	}

	/**
	 * Running the daily Summaries Query
	 * @param $widget
	 * @param $fromTo
	 * @return mixed
	 */
	public static function runWidgetFunction($widget, $fromTo, $period, $mac, $route, $tableName)
	{
		$reportData = null;
		// TODO: Put into cache
		// Generic Query will run in most widgets
		if(!is_null($tableName))
			$reportData = self::runReportQuery($fromTo, $period, $mac, $route, $tableName);

		// if Widget is null don't call return the Query as is
		if (is_null($widget))
			return $reportData;

		// Getting the function name from the widget
		$functionName = self::$widgetFunctionsArray[$widget];

		// Run the function giving the dailySummary
		return $functionName($reportData, $period, $route, $fromTo, $tableName, $mac);
	}


	/**
	 * Running the daily and hourly Summaries Query
	 * @param $fromTo
	 * @param $period
	 * @param $tableName if the widget has no data in the hourlySummaries table the isDailyOnly will be true
	 * @return mixed
	 */
	public static function runReportQuery( $fromTo, $period, $mac, $route, $tableName )
	{
		// If there is no settings widget enabled the default fromto is set to last week
		if(is_null($fromTo))
			$fromTo = [date('Y-m-d', strtotime('-1 week')), date('Y-m-d')];

		if($route == 'dashboard')
			//$reportData = DashboardStatsModel::select();
			$fromTo = [date('Y-m-d', strtotime('-1 months')), date('Y-m-d')];

		if($tableName == 'transaction_summaries')
			$reportData = TransactionSummariesModel::select()
				->whereBetween('report_date', $fromTo)
				->orderBy('report_date', 'ASC');

		elseif($tableName == 'hourly_hardware_health')
			$reportData = HourlyHardwareHealthModel::select()
				->whereBetween('report_date', $fromTo)
				->orderBy('report_date', 'ASC')
				->where('mac', $mac);

		elseif($tableName == 'bandwidth')
			$reportData = BandwidthModel::select()
				->orderBy('datetime', 'DSC')
				->where('mac', $mac)
				->take(30);

		elseif($period == 'last-24-hours' and $tableName == 'hourly_summaries')
			$reportData = HourlySummariesModel::select()
				->where(function($q) use($fromTo) {
					$q->where([
						['report_date', '=', $fromTo[0]],
						['hour_of_day', '>=', date ('H')]
					])
						->orWhere([
							['report_date', '=', $fromTo[1]],
							['hour_of_day', '<', date ('H')]
						]);
				})
				->orderBy('report_date', 'ASC')
				->orderBy('hour_of_day', 'ASC');

		else
			$reportData = DailySummariesModel::select()
				->whereBetween('report_date', $fromTo)
				->orderBy('report_date', 'ASC');

		if(session('admin.site.type') != 'site')
		{
			$reportData = $reportData->whereIn('site', session('admin.site.children'))
				->with('site')
				->groupBy('site');
		}
		else
			$reportData = $reportData->where(['site' => session('admin.site.loggedin')]);


		$reportData = $reportData->get();

		return $reportData;
	}

	/**
	 * Getting period chart category depending on period
	 * @param $period
	 * @param $newGuest
	 * @return false|string
	 */
	public static function getPeriodChartCategory($period, $newGuest)
	{
		if($period == 'last-24-hours')
			return $newGuest->hour_of_day;
		elseif($period == 'last-week')
			return date( 'D', strtotime($newGuest->report_date));
		elseif($period == 'last-month')
			return date( 'jS', strtotime($newGuest->report_date));
		elseif($period == 'last-year')
			return date( 'M jS', strtotime($newGuest->report_date));
		else
			return date( 'jS M y', strtotime($newGuest->report_date));
	}

	/**
	 * Create the CSV file, trigger the queue system
	 * @param $request Request
	 * @return bool
	 */
    public static function generateCSV(Request $request)
    {
        $siteId = (int)session('admin.site.loggedin');
		$siteName = \App\Models\AirConnect\Site::find($siteId)->name;
        $childrenIds = (array) session('admin.site.children');
        $userId = (int)session('admin.user.id');
        $email  = session('admin.user.username');

        // HTML rules
        $rules = [
            'period' => 'required',
            'type' => 'required'
        ];

        // collects all data from the form and do validation
        $requestData = $request->all();
        $validator = \Validator::make($requestData, $rules);

        // if validation fails it returns back to the same page and display the error
        if ($validator->fails()) {
            Messages::create('error', trans('admin.csv-retry'));
            return false;
        } else {
            //set the custom-period if there are dates selected
            if(!empty($requestData['period-from']) && !empty($requestData['period-to'])) {
                $requestData['period'] = 'custom-period';
            }
            //set the filename ( so the Job won't create more than one file )
            $filename = 'report_'.$siteId.'_'.mt_rand().'_'.$requestData['type'].'.csv';
            //start the queue job with the selected options
            dispatch(new CsvReportJob($requestData['type'], $requestData['period'], $requestData['period-from'], $requestData['period-to'], $siteName, $siteId, $childrenIds, $userId, $email, $filename));
            Messages::create('success', trans('admin.csv-success', ['email' => $email]));
            return true;
        }
    }

	/**
	 * Setting up the period array [$from, $to] from the period or the from and to values
	 * to be used later
	 * @param $period
	 * @param $from
	 * @param $to
	 * @param $prevFlag
	 * @return array
	 */
	public static function setupPeriod($period, $from, $to, $prevFlag = false)
	{
		if(is_null($period))
			return null;
		// When no comparing with previous period is needed
		if(!$prevFlag)
		{
			// If the period is not custom then the $to is Now
			if($period != 'custom-period')
				$to = date( 'Y-m-d' );

			// Calculating from depending on the period
			if ($period == 'last-24-hours')
				$from = date( 'Y-m-d', strtotime( '-1 day' ) );
			elseif ($period == 'last-2-days')
				$from = date( 'Y-m-d', strtotime( '-2 day' ) );
			elseif ($period == 'last-week')
				$from = date( 'Y-m-d', strtotime( '-1 week' ) );
			elseif ($period == 'last-month')
				$from = date( 'Y-m-d', strtotime( '-1 month' ) );
			elseif ($period == 'last-year')
				$from = date( 'Y-m-d', strtotime( '-1 year' ) );
		}
		else
		{
			// Calculationg the 'prev' from and to depending on the period
			if ($period == 'last-24-hours')
			{
				$from = date( 'Y-m-d', strtotime( '-2 day' ) );
				$to = date( 'Y-m-d', strtotime( '-1 day' ) );
			}
			elseif ($period == 'last-2-days')
			{
				$from = date( 'Y-m-d', strtotime( '-3 day' ) );
				$to = date( 'Y-m-d', strtotime( '-2 day' ) );
			}
			elseif ($period == 'last-week')
			{
				$from = date( 'Y-m-d', strtotime( '-13 day' ) );
				$to = date( 'Y-m-d', strtotime( '-1 week' ) );
			}
			elseif ($period == 'last-month')
			{
				$from = date( 'Y-m-d', strtotime( '-2 month + 1 day' ) );
				$to = date( 'Y-m-d', strtotime( '-1 month' ) );
			}
			elseif ($period == 'last-year')
			{
				$from = date( 'Y-m-d', strtotime( '-2 year + 1 month' ) );
				$to = date( 'Y-m-d', strtotime( '-1 year' ) );
			}
		}

		// If it is custom then the from and to are being directly returned
		return [$from, $to];
	}
}