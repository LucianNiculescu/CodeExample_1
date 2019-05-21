<?php


namespace App\Admin\Modules\Reports;
use App\Admin\Modules\Reports\Logic as Reports;

class CsvReports
{
	//Key = Type, Value = Permission
    public static $types = [
		'active-connections'			=> 'reports.csv',
        'bandwidth'						=> 'reports.csv',
        'browser-usage' 				=> 'reports.csv',
		'gender'						=> 'reports.csv',
		'latency'						=> 'reports.csv',
        'login-type'					=> 'reports.csv',
		'financial'						=> 'reports.csv.revenue-generated',
		'non-caravan-club-financial'	=> 'reports.csv.my-airangel-financial',
        'os'							=> 'reports.csv', //operatingSystems
        'packetloss'					=> 'reports.csv', //no, it's not a typo, packetloss is the translation from DB (while it was suppose to be packet-loss)
        'packages-sold'					=> 'reports.csv.packages-sold',
		'revenue-generated'				=> 'reports.csv.revenue-generated',
		'registered-guests'				=> 'reports.csv.registered-users',
		'guests-count'					=> 'reports.csv'
    ];

    /**
     * Getting the Array based on the selected report type
     * @param $type // can be 'Bandwidth' | 'Browser Usage' | 'Gender' | 'Latency' | etc
     * @param $siteName
     * @param $siteId
     * @param $childrenIds // Array with all the children of the site
     * @param $period // can be 'last-24-hours' | 'last-week' | 'last-month' | 'last-year'
     * @param $from
     * @param $to
     * @param $filename
     * @return array
     */
    public static function getReportData($type, $siteName, $siteId, $childrenIds, $period, $from, $to, $filename) {
        if(!array_key_exists($type,self::$types)) {
			\Log::info('Requested report type does not exists');
        	dd('Need a type for '.$type);
		}

        // Setting up the [$from, $to] array
        $fromTo = Reports::setupPeriod($period, $from, $to, false);
        $type = \App\Helpers\StringHelper::formatDashesToCamelCase($type);
        $class = '\App\Admin\Modules\Reports\CSV\\' .ucfirst($type);
        $typeObj = new $class($type, $siteName, $siteId, $childrenIds, $period, $fromTo, $filename);
        $reportData = $typeObj->reportData;
        return $reportData;
    }
}