<?php

namespace App\Admin\Modules\Reports\CSV;

use \App\Models\Reports\DailySummaries as DailySummariesModel;

class Bandwidth extends DefaultCSV
{

    /**
     * Generating the array with all the information used in the file
     * @return array //just add the array inside the protected member $this->reportData
     */
    protected function getReportData() {
        return DailySummariesModel::select(\DB::raw('SUM(`upload_total`) AS `Upload(Bytes)`'), \DB::raw('SUM(`download_total`) AS `Download(Bytes)`'), 'report_date AS Date')
            ->whereIn('site', $this->childrenIds)
            ->whereBetween('report_date', $this->fromTo)
            ->groupBy('report_date')
            ->get()
            ->toArray();
    }
}