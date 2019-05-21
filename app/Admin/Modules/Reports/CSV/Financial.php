<?php

namespace App\Admin\Modules\Reports\CSV;

use App\Models\AirConnect\Transaction;

class Financial extends DefaultCSV
{

    /**
     * Generating the array with all the information used in the file
     * @return array //just add the array inside the protected member $this->reportData
     */
    protected function getReportData() {
        return Transaction::select(
			'transaction.id',
			'transaction.guid',
			'transaction.package_id',
			'transaction.name',
			'transaction.user',
			'user.user AS email',
			'transaction.type',
			'transaction.status',
			'transaction.amount',
			'transaction.payment_type',
			'transaction.created',
			'site.reference AS nasid',
			'site.name AS site_name'
			)
			->leftjoin('airconnect.user','user.id', '=', 'transaction.user')
			->leftjoin('airconnect.site','site.id', '=', 'transaction.site')
			->whereIn('transaction.site', $this->childrenIds)
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