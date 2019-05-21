<?php
namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Admin\Helpers\CSV;
use App\Helpers\Email;

class UploadTranslationJob extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

	private $file;
	private $modelName;
	private $encode;
	private $username;
	/**
	 * Create a new job instance.
	 * @param $file //type of the report
	 * @param $modelName
	 * @param $encode
	 * @param $username
	 */
	public function __construct($file, $modelName, $encode, $username)
	{
		//set up the attributes of the CSV Report base on its type
		$this->file = $file;
		$this->modelName = $modelName;
		$this->encode = $encode;
		$this->username = $username;
	}

	/**
	 * Execute the job.
	 * @return void
	 */
	public function handle()
	{
		//start the Log for the Job if we are on debug
		if(config('app.debug')){
			\Log::info("User ".$this->username." has uploaded a Translation");
			$startTime = microtime(true);
		}

		//Upload the file to the DB
		$uploaded = CSV::uploadToDB($this->file, $this->modelName , $this->encode);
		$text = 'failed to upload';
		if(!empty($uploaded)) {
			$text = 'successfully uploaded';
			//Clear the cache if the file has been successfully uploaded
			\App\Admin\DevTools\Cache\Controller::clearTranslations();
			self::sendEmail();
		}

		//Remove the temp file that has been created
		\File::delete($this->file);
		//Write the successful/failed Log if we are on debug
		if(config('app.debug')) {
			$endTime = microtime(true);
			\Log::info("User ".$this->username." has {$text} Translation file and it took ".sprintf("%.9f", ($endTime-$startTime))." seconds.");
		}
	}

	/**
	 * Setting the subject and the bodyContent for the email and send it
	 * @return String Error / True
	 */
	private function sendEmail() {
		$subject = trans('admin.translations-email-subject');
		$bodyContent = trans('admin.translations-email-body');
		$tags = ['Upload-translations'];
		$sent = Email::send($this->username, $subject, null, null, $bodyContent, $tags);
		return $sent ?? true;
	}

	/**
	 * Function that is called when the Job fails
	 */
	public function failed()
	{
		\Log::info('UploadTransactionJob has failed to complete.');
	}
}
