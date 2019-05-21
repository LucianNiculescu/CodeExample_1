<?php

namespace App\Admin\Modules\Reports\CSV;


use App\Helpers\DateTime;
use App\Helpers\StringHelper;

class DefaultCSV
{
    public $reportData = [];
    protected $type;
    protected $filename;
	protected $siteName;
    protected $siteId;
    protected $childrenIds;
    protected $period;
    protected $fromTo;

    /**
     * Values needed when we create a CSV object
     * @param $type // can be 'bandwidth' | 'browserUsage' | 'gender' | 'latency' | etc
     * @param $siteName
     * @param $siteId
     * @param $childrenIds
     * @param $period // can be 'last-24-hours' | 'last-week' | 'last-month' | 'last-year'
     * @param $fromTo
     * @param $filename
     */
    public function __construct($type, $siteName, $siteId, $childrenIds, $period, $fromTo, $filename)
    {
        $this->type = $type;
        $this->siteId = $siteId;
		$this->siteName = $siteName;
        $this->childrenIds = $childrenIds;
        $this->period = $period;
        $this->fromTo = $fromTo;
        $this->filename = $filename;
        $this->reportData = $this->getReportData();
		if(empty($this->reportData)) {
			$this->reportData[0]["No records for this period"] = '';
			\Log::info("Report for site {$this->siteId} from {$this->fromTo[0]} to {$this->fromTo[1]} has no records.");
		}
        $this->setCsvFooter();
    }

    protected function getReportData() {
        dd('You shouldn\'t get here!');
        return [];
    }

    /**
     * Setting a footer with some information about the selected report
     * @param $title
     * @param $filename
     * @param $siteName
     * @param $from
     * @param $to
     */
    private function setCsvFooter() {
        $data = [
            ''             => '',
            'Info'       => '',
            'Title'      => ucfirst(StringHelper::formatCamelCaseToSpaces($this->type)),
            'Filename'   => $this->filename,
            'Site'       => $this->siteName,
            'Start Date' => DateTime::medium($this->fromTo[0]),
            'End Date'   => DateTime::medium($this->fromTo[1])
        ];
        $footer = [];
        foreach($data as $key => $value) {
            $footer[] = $key;
            $footer[] = $value;
        }
        $val = array_chunk($footer,2);
        foreach($val as $item)
            $this->reportData[] = $item;
    }


}