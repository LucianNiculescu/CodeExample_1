<?php

namespace App\Admin\Modules\Reports\CSV;

use App\Models\AirConnect\Transaction;

class RevenueGenerated extends DefaultCSV
{

    /**
     * Generating the array with all the information used in the file
     * @return array //just add the array inside the protected member $this->reportData
     */
    protected function getReportData() {
        return Transaction::select( \DB::raw('DISTINCT guid '), 'site', \DB::raw('SUM(amount) AS amount'), 'created')
            ->whereIn('site', $this->childrenIds)
            ->whereBetween('created', $this->fromTo)
            ->groupBy(\DB::raw('DAY(created)'), 'site')
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();
    }
}