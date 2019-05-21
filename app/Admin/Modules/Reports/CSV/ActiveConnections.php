<?php

namespace App\Admin\Modules\Reports\CSV;

use \App\Models\AirHealth\Views\ConcurrentUsers as ConcurrentUsers;

class ActiveConnections extends DefaultCSV
{

    /**
     * Generating the array with all the information used in the file
     * @return array //just add the array inside the protected member $this->reportData
     */
    protected function getReportData() {
        return ConcurrentUsers::select('name', 'site', \DB::raw('date_format(report_date,"%b") AS `month`'), \DB::raw('MAX(active_connections) AS `Active Connections`'))
            ->whereIn('site', $this->childrenIds)
            ->whereBetween('report_date', $this->fromTo)
			->whereNotNull('active_connections')
            ->groupBy('site', 'month')
            ->get()
            ->toArray();
    }
}