<?php

namespace App\Admin\Modules\Reports\CSV;

use App\Models\AirConnect\User;
use App\Admin\Modules\Reports\Logic as Reports;

class LoginType extends DefaultCSV
{
	/**
	 * User status constants
	 */
	const USER_STATUS_ACTIVE = 'active';
	const USER_STATUS_INACTIVE = 'inactive';
	const USER_STATUS_BLOCKED = 'blocked';
	const USER_STATUS_RESTRICTED = 'restricted';
	const USER_STATUS_ORPHANED = 'orphaned';

	/**
     * Generating the array with all the information used in the file
     * @return array //just add the array inside the protected member $this->reportData
     */
    protected function getReportData() {
    	//We extract just the login types from the central list in Reports::Logic
		// Note that those login types do not currently include Airpass, which is v1 only
    	$validLoginTypes = array_keys(Reports::loginTypes);

		// We want all logins within the reporting period where the user is active
		$loginTypes =  User::select('site', \DB::raw('COUNT(distinct user.id) as typeCount'), 'user_attribute.name AS type')
			->join('user_attribute', 'user_attribute.ids', '=', 'user.id')
			->whereIn('user.site', $this->childrenIds)
			->whereBetween('user.created', $this->fromTo)
			->where('user.status', '=', self::USER_STATUS_ACTIVE)
			->whereIn('user_attribute.name', $validLoginTypes)
			->groupBy('site')
			->orderBy('user_attribute.name', 'desc')
			->get()
			->toArray();

		if(!empty($loginTypes))
			return $this->setAirpassToEmail($loginTypes);
		else
			return [];
    }

	/**
	 * Set the type to 'Email' where type is 'Airpass'
	 * @param $results
	 * @return mixed
	 */
	private function setAirpassToEmail($results) {
    	foreach($results as $key => $val) {
    		if($val['type'] === 'Airpass')
    			$val['type'] = 'Email';
		}
		return $results;
	}
}