<?php
namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\Email;

class EmailJob extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;


	private $from;
	private $to;
	private $subject;
	private $textBody;
	private $htmlBody;
	private $tags;

	/**
	 * Create a new job instance.
	*/
	public function __construct($from=null, $to=null, $subject=null, $textBody=null, $htmlBody=null, $tags=[])
	{
		$this->from 	= $from;
		$this->to 		= $to;
		$this->subject 	= $subject;
		$this->textBody = $textBody;
		$this->htmlBody = $htmlBody;
		$this->tags 	= $tags;

	}

	/**
	 * Execute the job.
	 * @return void
	 */
	public function handle()
	{
		$this->sendEmail();
	}

	/**
	* Sending the email
	 */
	private function sendEmail() {
		$sent = Email::send($this->to, $this->subject, $this->textBody, $this->from, $this->htmlBody, $this->tags);
		if(config('app.debug'))
			\Log::info("Email Sent - " . $sent ?? '');

		return $sent ?? true;
	}
}
