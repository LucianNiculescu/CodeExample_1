<?php
namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Admin\Modules\Reports\CsvReports;
use App\Admin\Helpers\CSV;
use App\Helpers\Email;
use App\Helpers\UrlHelper;

class CsvReportJob extends Job implements ShouldQueue {
	use InteractsWithQueue, SerializesModels;

	private $path 		= 'uploads/reports/csv/';
	private $realpath 	= null;
	private $type;
	private $period;
	private $from;
	private $to;
	private $siteName;
	private $siteId;
	private $childrenIds;
	private $userId;
	private $email;
	private $filename;

	/**
	 * Create a new job instance.
	 * @param $type //type of the report
	 * @param $period
	 * @param $from //start date
	 * @param $to   //end date
	 * @param $siteName
	 * @param $siteId
	 * @param $childrenIds
	 * @param $userId
	 * @param $email
	 * @param $filename
	 */
	public function __construct($type, $period, $from, $to, $siteName, $siteId, $childrenIds, $userId, $email, $filename) {
		//create the folder if it doesn't exists
		if(!\File::isDirectory(public_path().'/'.$this->path))
			\File::makeDirectory(public_path().'/'.$this->path, 0777, true, true);

		//set up the attributes of the CSV Report base on its type
		$this->type = $type;
		$this->period = $period;
		$this->from = $from;
		$this->to = $to;
		$this->siteName = $siteName;
		$this->siteId = $siteId;
		$this->childrenIds = $childrenIds;
		$this->userId = $userId;
		$this->email = $email;
		$this->filename = $filename;
		$this->realpath = $this->path.$this->filename;
	}

	/**
	 * Execute the job.
	 * @return void
	 */
	public function handle() {
		//start the Log for the Job if we are on debug
		if(config('app.debug')){
			\Log::info("User ".$this->userId." has started generating CSV ".$this->type." Report on site ".$this->siteId);
			$startTime = microtime(true);
		}

		//Generate CSV Data and create the file if it doesn't exists
		if(!\File::exists(public_path().'/'.$this->realpath)) {
			$data = CsvReports::getReportData($this->type, $this->siteName, $this->siteId, $this->childrenIds, $this->period, $this->from, $this->to, $this->filename);

			// If data is empty handle it
			if( is_null($data) || $data == '' )
				$data = [];

			CSV::createFile($data, $this->realpath);
		} else {
			if(config('app.debug')) {
				\Log::info("The file already exists.");
			}
		}

		//If the file exists
		if(\File::exists(public_path().'/'.$this->realpath)) {
			//Get the url and send the Email
			$url = UrlHelper::getUrl($this->userId);
			$url = ($url) ? $url.$this->realpath : config('app.url').'/';
			if(config('app.env') !== 'local')
				$this->sendEmail($url);

			//End Log of the Job
			$text = 'generated';
		} else {
			$text = 'failed generating the file because the file has not been created, ';
		}

		//Write the successful/failed Log if we are on debug
		if(config('app.debug')) {
			$endTime = microtime(true);
			\Log::info("User ".$this->userId." has ".$text." CSV ".$this->type." Report on site ".$this->siteId." and it took ".sprintf("%.9f", ($endTime-$startTime))." seconds.");
		}
	}

	/**
	 * Setting the subject and the bodyContent for the email and send it
	 * @param $url
	 * @return String Error / True
	 */
	private function sendEmail($url) {
		$subject = trans('admin.csv-email-subject', ['type'=>$this->type, 'site'=>$this->siteName]);
		$bodyContent = trans('admin.csv-email-body', ['url'=>$url]);
		$tags = ['CSV-Report', 'site-'.$this->siteId];
		$sent = Email::send($this->email, $subject, $bodyContent, null, $bodyContent, $tags);
		return $sent ?? true;
	}

	/**
	 * Setting the subject and the bodyContent for the failed job email and send it
	 * @return String Error / True
	 */
	private function sendFailedJobEmail() {
		$subject = trans('admin.failed-csv-email-subject', ['type'=>$this->type, 'site'=>$this->siteName]);
		$bodyContent = trans('admin.failed-csv-email-body');
		$tags = ['CSV-Report', 'site-'.$this->siteId];
		$sent = Email::send($this->email, $subject, $bodyContent, null, $bodyContent, $tags);
		return $sent ?? true;
	}

	/*
	 * The function that is called when the job fails (after 3 retries)
	 * Erases the file that has been created and send an Email to inform the user
	 */
	public function failed() {
		if(\File::exists(public_path().'/'.$this->realpath))
			\File::delete(public_path().'/'.$this->realpath);
		\Log::info('CsvReportJob has failed to complete, the file has been erased');

		//Send an email to the user to tell him of the failed CSV Report
		if(config('app.env') !== 'local')
			$this->sendFailedJobEmail();
	}
}
