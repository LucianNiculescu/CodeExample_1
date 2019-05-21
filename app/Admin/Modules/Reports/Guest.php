<?php
namespace App\Admin\Modules\Reports;

use \App\Admin\Modules\Reports\Logic as Reports;
use \App\Models\Radius\Radcheck;
use Carbon\Carbon;

class Guest
{
	/**
	 * Getting Demographics Data from the daily Summary
	 * @param $reportData
	 * @return array
	 */
	public static function getDemographicsData($reportData)
	{
		// Zero the total for each login type
		foreach (array_keys(Reports::loginTypes) as $loginType) {
			$loginTotal[$loginType] = 0;
		}

		foreach ($reportData as $newGuest) {
			foreach (Reports::loginTypes as $loginType=>$attributes) {
				$loginTotal[$loginType] += $newGuest->{$attributes['db_column']};
			}
		}

		// Build the json response using the translated login type as key and the total as value
		$arrReturn = [];
		foreach (Reports::loginTypes as $loginType=>$attributes) {
			if ($loginTotal[$loginType] !== 0 ) {
				$arrReturn[] = [number_format($loginTotal[$loginType]), $attributes['icon'], trans('admin.' . $loginType)];
			}
		}

		return $arrReturn;
	}

	/**
	 * Getting New Guest Data from the daily Summary
	 * @param $reportData
	 * @return array
	 */
	public static function getNewGuestsData($reportData, $period, $route, $fromTo, $tableName)
	{
		$newGuests 	= [];
		$prevGuests = [];
		$categories = [];

		if($period != 'custom-period')
		{
			// Getting Previous Data
			$prevFromTo = Reports::setupPeriod($period, $fromTo[0] , $fromTo[1], true);

			$prevReportData = Reports::runWidgetFunction( null, $prevFromTo, $period , null, null, $tableName);

			$prevRegisteredUsers = $prevReportData;//->get();

			foreach($prevRegisteredUsers as $prevGuest) {
				$prevGuests[] = intval($prevGuest->registered_users);
			}
		}

		foreach($reportData as $newGuest)
		{
			$newGuests[] = intval($newGuest->registered_users);

			$categories[] =  Reports::getPeriodChartCategory($period, $newGuest);
		}

		return [$categories, $newGuests, $prevGuests];
	}

	/**
	 * Getting Accumulated Guest Data from the daily Summary
	 * @param $reportData
	 * @return array
	 */
	public static function getAccumulatedGuestsData($reportData, $period)
	{
		$accumulatedGuests = [];
		$accumulatedValue = 0;
		$categories = [];


		foreach($reportData as $newGuest)
		{
			$accumulatedValue = $accumulatedValue + $newGuest->registered_users;
			$accumulatedGuests[] = $accumulatedValue;

			$categories[] =  Reports::getPeriodChartCategory($period, $newGuest);
		}

		return [$categories, $accumulatedGuests];
	}

	/**
	 * Getting RegUsersData from the daily Summary
	 * @param $reportData
	 * @return array
	 */
	public static function getRegisteredUsersData($reportData, $period, $route, $fromTo, $tableName)
	{
		$sumRregisteredUsers = $reportData->sum('registered_users');

		if (is_null($sumRregisteredUsers))
			$sumRregisteredUsers = 0;

		if($period != 'custom-period')
		{
			// Setting up the [$from, $to] array
			$prevFromTo = Reports::setupPeriod($period, $fromTo[0] , $fromTo[1], true);

			$prevReportData = Reports::runWidgetFunction( null, $prevFromTo, $period , null, null, $tableName);

			$sumPrevRregisteredUsers = $prevReportData->sum('registered_users');

			if (is_null($sumPrevRregisteredUsers))
				$sumPrevRregisteredUsers = 0;

			if($sumPrevRregisteredUsers > 0)
				$trendPercentage = round(( $sumRregisteredUsers - $sumPrevRregisteredUsers )/$sumPrevRregisteredUsers * 100);
			else
				$trendPercentage = 100;
		}

		return [number_format($sumRregisteredUsers), $trendPercentage ?? 0];
	}

	/**
	 * Getting Dwelltimedata from the daily Summary
	 * @param $reportData
	 * @return float
	 */
	public static function getDwellTimeData($reportData)
	{
		// Getting average connection time
		$averageConnectionTime = $reportData->avg('avg_connection_time');

		// Converting result into hours
		return gmdate("H:i", $averageConnectionTime);
	}

	/**
	 * Getting Gender Data from the daily Summary
	 * @param $reportData
	 * @return array
	 */
	public static function getGenderData($reportData)
	{
		// Getting registered (male/female) users
		$regUsersMale 	= $reportData->sum('reg_users_male');
		$regUsersFemale = $reportData->sum('reg_users_female');

		if($regUsersMale + $regUsersFemale == 0)
			return [0, 0];
		// Calculating the percentage for the female and male registered users
		$malePercentage = ceil(($regUsersMale 	 / ($regUsersMale + $regUsersFemale)) * 100);
		$femalePercentage = 100 - $malePercentage;
		return[	$femalePercentage, $malePercentage];

	}

	/**
	 * Getting totals for each type of login
	 * @param $reportData
	 * @return array|int
	 */
	public static function getLoginTypesData($reportData)
	{
		// Zero the total for each login type
		foreach (array_keys(Reports::loginTypes) as $loginType ) {
			$loginTotal[$loginType] = 0;
		}

		foreach ($reportData as $newGuest) {
			foreach (Reports::loginTypes as $loginType=>$attributes) {
				$loginTotal[$loginType] += $newGuest->{$attributes['db_column']};
			}
		}

		// Build the json response using the translated login type as key and the total as value
		$jsonReturn = [];
		foreach (array_keys(Reports::loginTypes) as $loginType ) {
			// Exclude login-type all-guests as it encompasses all other types.
			if ($loginTotal[$loginType] !== 0 && $loginType != 'all-guests') {
				$jsonReturn[trans('admin.' . $loginType)] = $loginTotal[$loginType];
			}
		}

		return json_encode($jsonReturn);
	}

    /**
     * Getting "logins in last n" Data from the radcheck
     * We build an array of the periods to check then perform queries for each.
     * We just use numerical indices and use the translations to match the period calculation.
     * This means we won't need to delete and add translations when we change the period - we just edit them.
     * @param $reportData
     * @return array
     */
    public static function getLoginsInLastNData($reportData, $period, $route, $fromTo, $tableName)
    {
        $now = Carbon::now();
		$site = session('admin.site.loggedin');

        //Define the number of login periods to accumulate. We have translations using the index. translation
        $login_periods_count = 4;
        for ($period_index=1; $period_index <= $login_periods_count; $period_index++) {
            $logins_since[$period_index] = $now;
        }

        // Index 1 is ten minutes ago
        $logins_since[1] = $logins_since[1]->subMinute(10);

        // Index 2 is one hour ago
        $logins_since[2] = $logins_since[2]->subHour();

        // Index 3 is six hours ago
        $logins_since[3] = $logins_since[3]->subHours(6);

        // Index 4 is one day ago
        $logins_since[4] = $logins_since[4]->subDay();

        // We need to collect data for both this site and for all sites
        $recent_logins = [];

        for ($period_index=1; $period_index <= $login_periods_count; $period_index++) {
			// Get label (description for this period
			$period_label = trans('admin.logins-in-last-n-' . $period_index);
			$recent_logins[$period_index]["label"] = $period_label;

			// Count logins for this period on this site
            $recent_logins[$period_index]["site"] = Radcheck::site($site)->since($logins_since[$period_index])->count();

			// Count logins for this period for all sites
			$recent_logins[$period_index]["all"] = Radcheck::siteWithVersion3()->since($logins_since[$period_index])->count();
        }

        return $recent_logins;
    }

}