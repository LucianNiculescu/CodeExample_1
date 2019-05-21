<?php

namespace App\Admin\Modules\Reports\CSV;

use App\Models\AirConnect\User;

class GuestsCount extends DefaultCSV
{
	/**
	 * User status constants
	 */
	const USER_STATUS_ACTIVE 		= 'active';
	const USER_STATUS_INACTIVE 		= 'inactive';
	const USER_STATUS_BLOCKED 		= 'blocked';
	const USER_STATUS_RESTRICTED 	= 'restricted';
	const USER_STATUS_ORPHANED 		= 'orphaned';

    /**
     * Generating the array with all the information used in the file
     * @return array //just add the array inside the protected member $this->reportData
     */
    protected function getReportData() {
		return User::select('site', 'site.name as site_name', \DB::raw('COUNT(user.created) as count'), \DB::raw('DATE(user.created) as date'))
			->leftJoin('site', function ($join) {
				$join->on('site', '=', 'site.id');
			})
			->whereIn('site', $this->childrenIds)
			->whereBetween('user.created', $this->fromTo)
			->where('user.status', '=', self::USER_STATUS_ACTIVE)
			->groupBy(\DB::raw('DAY(user.created)'), 'site')
			->orderBy('site','desc')
			->orderBy('user.created', 'desc')
			->get()
			->toArray();
    }
}