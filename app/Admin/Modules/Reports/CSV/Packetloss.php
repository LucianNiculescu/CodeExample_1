<?php

namespace App\Admin\Modules\Reports\CSV;

use App\Models\AirHealth\HardwareHistory;

class Packetloss extends DefaultCSV
{

    /**
     * Generating the array with all the information used in the file
     * @return array //just add the array inside the protected member $this->reportData
     */
    protected function getReportData() {

		return HardwareHistory::select('hardware.mac', 'hardware.site', \DB::raw('AVG(hardware_history.packetloss) as packetloss'), 'hardware_history.updated')
			->join('hardware', 'hardware.mac', '=', 'hardware_history.mac')
			->whereIn('hardware.site', $this->childrenIds)
			->whereBetween('hardware_history.updated', $this->fromTo)
			->groupBy(\DB::raw('DAY(hardware_history.updated), hardware.mac, hardware.site'))
			->orderBy('hardware_history.updated', 'asc')
			->get()
			->toArray();
    }
}