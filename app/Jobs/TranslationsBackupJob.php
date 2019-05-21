<?php
namespace App\Jobs;

use App\Models\AirConnect\AdminTemplate;
use App\Models\AirConnect\Translation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Admin\Helpers\CSV;
use App\Helpers\Email;

class TranslationsBackupJob extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

	private $path = 'uploads/reports/translations/';
	private $realpath = null;

	/**
	 * Create a new job instance.
	 */
	public function __construct() {

		//create the folder if it doesn't exists
		if(!\File::isDirectory(public_path().'/'.$this->path))
			\File::makeDirectory(public_path().'/'.$this->path, 0777, true, true);

		//set the realpath with the filename
		$this->realpath = $this->path.'Translation_'.\Carbon\Carbon::now()->format('l').'.csv';
	}

	/**
	 * Execute the job.
	 * @return void
	 */
	public function handle() {
		//start the Log for the Job if we are on debug
		if(config('app.debug')){
			\Log::info("TranslationsBackupJob for  ".\Carbon\Carbon::now()->toDateTimeString()." has started.");
			$startTime = microtime(true);
		}

		//Generate Backup of Translations
		$data = Translation::all()->toArray();

		// If data is empty handle it
		if( is_null($data) || $data == '' )
			$data = [];

		CSV::createFile($data, $this->realpath);

		//If the file exists
		if(\File::exists(public_path().'/'.$this->realpath)) {

			//Get the url
			$url = AdminTemplate::where('name', 'airangel')->first();
			if(!empty($url->url))
				$http = ($url->http == 0)? 'https://' : 'http://';

			//Check if the Url exists and send the email
			if(!empty($http)) {
				$realUrl = $http.$url->url.'/'.$this->realpath;
				$this->sendEmail($realUrl);
			}

			//End Log of the Job
			$text = 'generated';
		} else
			$text = 'failed generating the file because the file has not been created, ';

		//Write the successful/failed Log if we are on debug
		if(config('app.debug')) {
			$endTime = microtime(true);
			\Log::info("TranslationsBackupJob for ".\Carbon\Carbon::now()->toDateTimeString()." has ".$text." and it took ".sprintf("%.9f", ($endTime-$startTime))." seconds.");
		}
	}

	/*
	 * The function that is called when the job fails (after 3 retries)
	 * Erases the file that has been created
	 */
	public function failed() {
		if(\File::exists(public_path().'/'.$this->realpath))
			\File::delete(public_path().'/'.$this->realpath);
		\Log::info('TranslationsBackupJob has failed to complete, the file has been erased');
	}

	/**
	 * Setting the subject and the bodyContent for the email and send it
	 * @param $url
	 * @return String Error / False
	 */
	private function sendEmail($url) {
		$subject = 'Translations Backup for '.\Carbon\Carbon::now()->toDateTimeString();
		$bodyContent = '<h3>Translations for '.\Carbon\Carbon::now()->toDateTimeString().'</h3><p>You can download the Translations from <br> '.$url.'</p>';
		$tags = ['TranslationsBackupJob'];
		$sent = Email::send('applications@airangel.com', $subject, null, null, $bodyContent, $tags);
		return $sent ?? true;
	}
}
