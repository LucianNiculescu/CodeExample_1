<?php

namespace App\Admin\Modules\Reports\CSV;

use App\Models\AirConnect\Site;
use App\Models\AirConnect\Transaction;

class NonCaravanClubFinancial extends DefaultCSV
{

    /**
     * Generating the array with all the information used in the file
     * @return array //just add the array inside the protected member $this->reportData
     */
    protected function getReportData() {
		//Get the estate of Caravan Club
    	$sites = Site::select(\DB::raw('group_concat(id)'))
			->where('parent', 25)
			->get();

    	return Transaction::select(
			'transaction.id',
			'site.id AS site_id',
			'site.name AS site_name',
			'transaction.guid',
			'transaction.package_id',
			'transaction.name',
			'transaction.user',
			'user.user AS email',
			'transaction.type',
			'transaction.status',
			'transaction.amount',
			'transaction.payment_type',
			'transaction.created'
			)
			->leftjoin('airconnect.user','user.id', '=', 'transaction.user')
			->leftjoin('airconnect.site','site.id', '=', 'transaction.site')
			->whereNotIn('transaction.site', $sites)
			->where('transaction.amount', '>', 0)
			->whereRaw("(transaction.status = 'Completed'
				OR transaction.status = 'Refunded'
				OR transaction.status = 'Recurring'
				OR transaction.status = 'Complimentary')")
			->where('transaction.payment_type', '<>', 'pms-complete')
			->whereBetween('transaction.created', $this->fromTo)
			->groupBy('transaction.guid', 'transaction.site')
			->orderBy('site.reference')
			->get()
			->toArray();
    }
}