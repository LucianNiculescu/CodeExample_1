<?php

namespace App\Admin\Modules\Reports\CSV;

use App\Models\AirConnect\Browser;

class BrowserUsage extends DefaultCSV
{

    /**
     * Generating the array with all the information used in the file
     * @return array //just add the array inside the protected member $this->reportData
     */
    protected function getReportData() {
        return Browser::select('site', 'browser', \DB::raw('COUNT(browser) AS count'))
            ->whereIn('site', $this->childrenIds)
            ->whereBetween('created', $this->fromTo)
            ->groupBy('browser', 'site')
            ->orderBy('browser', 'desc')
            ->get()
            ->toArray();
    }
}