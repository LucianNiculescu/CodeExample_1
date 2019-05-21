<?php

namespace App\Admin\Modules\Reports\CSV;

use App\Models\AirConnect\Browser;

class Os extends DefaultCSV
{

    /**
     * Generating the array with all the information used in the file
     * @return array //just add the array inside the protected member $this->reportData
     */
    protected function getReportData() {
        return Browser::select('site', 'platform', \DB::raw('COUNT(platform) AS count'))
            ->whereIn('site', $this->childrenIds)
            ->whereBetween('created', $this->fromTo)
            ->groupBy('platform', 'site')
            ->orderBy('platform', 'desc')
            ->get()
            ->toArray();
    }
}