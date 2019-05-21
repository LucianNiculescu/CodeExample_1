<?php
namespace App\Admin\Modules\Reports;


use App\Helpers\CurrencyHelper;
use \App\Admin\Modules\Reports\Logic as Reports;

class Financial
{
	/**
	 * Getting the Net packages sold vs free Data from the reportData
	 * @param $reportData
	 * @param $period
	 * @return int|string
	 */
	public static function getNetPackagesData($reportData, $period, $route)
	{
		if($reportData->isEmpty())
			return [0, 0];

		if($route == 'dashboard')
		{
			$packagesPaidPercent = round($reportData->first()->packages_paid_percent);
			$packagesFreePercent = round($reportData->first()->packages_free_percent);
			return [number_format( $packagesPaidPercent ).'%', number_format( $packagesFreePercent ).'%'];
		}
		else
		{
			$freePackages = 0;
			$paidPackages = 0;

			foreach($reportData as $transaction)
				if($transaction->package_cost == 0)
					$freePackages += $transaction->packages_sold;
				else
					$paidPackages += $transaction->packages_sold;


			return [number_format( $paidPackages ), number_format( $freePackages )];
		}
	}

	/**
	 * Getting the Most used Packages Data from the reportData
	 * @param $reportData
	 * @return int|string
	 */
	public static function getMostUsedPackageData($reportData)
	{
		if($reportData->isEmpty())
			return trans('admin.no-data-found');

		$mostUsedPackage = [];

		foreach($reportData as $transaction)
			if($transaction->package_cost > 0)
				$mostUsedPackage[] = $transaction->package_name;

		// Find most common package
		$mostUsedPackage = array_count_values($mostUsedPackage); // Get most used paid package
		asort($mostUsedPackage); // Sort and count occurrences
		end($mostUsedPackage); // Find package with highest occurrence
		$mostUsedPackage = key($mostUsedPackage);

		return $mostUsedPackage;
	}

	/**
	 * Getting the Net Income Data from the reportData
	 * @param $reportData
	 * @return int|string
	 */
	public static function getNetIncomeData($reportData)
	{
		if($reportData->isEmpty())
			return CurrencyHelper::getCurrencySymbol() . '0';

		$netIncome = 0;

		foreach($reportData as $transaction)
			if($transaction->packages_sold > 0 and $transaction->package_cost > 0 )
			$netIncome += $transaction->packages_sold * $transaction->package_cost;

		return CurrencyHelper::getCurrencySymbol() . number_format(round($netIncome, 2), 2) ;
	}

	/**
	 * Getting the Average Net Income Data from the reportData
	 * @param $reportData
	 * @param $period
	 * @param $route
	 * @return int|string
	 */
	public static function getAverageNetIncomeData($reportData, $period, $route)
	{
		if($reportData->isEmpty())
			return CurrencyHelper::getCurrencySymbol() . '0';

		if($route == 'dashboard')
			return CurrencyHelper::getCurrencySymbol() . $reportData->first()->packages_avg_revenue ;
		else
			return CurrencyHelper::getCurrencySymbol() . number_format(round($reportData->avg('net_income'), 2), 2) ;
	}

	/**
	 * Getting Cumulative Net Income Data
	 * @param $reportData
	 * @return array
	 */
	public static function getCumulativeNetIncomeData($reportData, $period)
	{
		$cumulativeNetIncome = [];
		$netIncome = 0;
		$categories = [];


		foreach($reportData as $transaction)
		{
			$netIncome = $netIncome + $transaction->net_income;
			$cumulativeNetIncome[] = $netIncome;

			$categories[] =  Reports::getPeriodChartCategory($period, $transaction);
		}

		return [$categories, $cumulativeNetIncome];
	}


	/**
	 * Getting DailyCashflow Data
	 * @param $reportData
	 * @return array
	 */
	public static function getDailyCashflowData($reportData, $period)
	{
		$cashFlow = [];
		$categories = [];


		foreach($reportData as $transaction)
		{
			$cashFlow[] = (float)$transaction->net_income;

			$categories[] =  Reports::getPeriodChartCategory($period, $transaction);
		}

		return [$categories, $cashFlow];
	}


	/**
	 * Getting PackageSalesIncome Data
	 * @param $reportData
	 * @return array
	 */
	public static function getPackageSalesIncomeData($reportData)
	{
		$packageSales 	= [];
		$packageIncome	= [];
		$categories = [];


		$reportData = $reportData->groupBy('package_name');

		foreach($reportData as $packageName => $data)
		{
			$sales  = 0;
			$income = 0;

			foreach($data as $transaction)
			{
				if($transaction->package_cost > 0 and $transaction->gross_revenue)
				{
					if(!in_array($packageName, $categories))
						$categories[] = $packageName;
					$sales  += $transaction->packages_sold;
					$income += $transaction->gross_revenue;
				}
			}

			if($sales > 0 )
				$packageSales[] 	= $sales;

			if($income > 0 )
				$packageIncome[]	= $income;


		}

		return [$categories, $packageSales, $packageIncome];
	}
}