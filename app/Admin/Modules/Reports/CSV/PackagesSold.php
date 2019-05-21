<?php

namespace App\Admin\Modules\Reports\CSV;

use App\Models\AirConnect\Transaction;

class PackagesSold extends DefaultCSV
{

    /**
     * Generating the array with all the information used in the file
     * @return array //just add the array inside the protected member $this->reportData
     */
    protected function getReportData() {
        return Transaction::select('site', 'name', \DB::raw('COUNT(name) AS sold'), 'amount AS cost')
            ->whereIn('site', $this->childrenIds)
            ->whereBetween('created', $this->fromTo)
            ->groupBy('amount', 'name', 'site')
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();
    }
}