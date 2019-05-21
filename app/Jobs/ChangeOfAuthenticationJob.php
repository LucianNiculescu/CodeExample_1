<?php

namespace App\Jobs;

use App\Models\AirConnect\Admin as AdminModel;
use App\Helpers\Email;
use App\Admin\Modules\Gateways\Types\Logic as Types;
use App\Models\AirConnect\Gateway;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;

class ChangeOfAuthenticationJob extends Job implements ShouldQueue
{
	use InteractsWithQueue, Queueable, SerializesModels;

	/**
	 * The properties that has to be set for this job
	 *
	 */
	protected $gateway;
	protected $upload;
	protected $download;
	protected $guid;
	protected $userId;
	protected $port;

	/**
	 * ChangeOfAuthenticationJob constructor.
	 * @param $gateway
	 * @param $upload
	 * @param $download
	 * @param $guid
	 * @param $userId
	 * @param $port
	 */
	public function __construct(Gateway $gateway, $upload, $download, $guid, $userId, $port = '')
	{
		$this->gateway = $gateway;
		$this->upload = $upload;
		$this->download = $download;
		$this->guid = $guid;
		$this->userId = $userId;
		$this->port = $port;

		//This is the actual handle() method that is not working 100% of the time
		$result = true;

		$gatewayApiObject = Types::getGatewayApiObject($this->gateway->toArray());

		if(!is_null($gatewayApiObject))
			$result = $gatewayApiObject->changeBandwidth($this->upload, $this->download, $this->guid, $this->port);

		//Send an email with the errors
		if($result !== true)
			//Get the url and send the Email
			$this->sendFailedJobEmail($result);
	}

	/**
	 * Execute the job
	 *
	 * @return void
	 */
	public function handle()
	{
		//Because this method is not triggered all the time (and I have no logical explanation for it, sometimes it's working with logging, sometimes with a for loop)
		// I have moved the functionality of this method to the constructor
	}

	/**
	 * Setting the subject and the bodyContent for the failed job email and send it
	 * @param array|string $errors
	 * @return String Error / True
	 */
	private function sendFailedJobEmail($errors = '') {
		if(config('app.env') !== 'local') {
			//Get the admin email
			$admin = AdminModel::find($this->userId);
			//Default error message
			if(empty($errors))
				$errors = trans('admin.coa-failed');

			$subject = trans('admin.coa-failed');
			$bodyContent = json_encode($errors);
			$tags = ['CoA-Error', 'guid-'.$this->guid];
			$sent = Email::send($admin->username, $subject, $bodyContent, null, $bodyContent, $tags);
			return $sent ?? true;
		}
		return true;
	}

	/*
	 * The function that is called when the job fails (after 3 retries)
	 */
	public function failed()
	{
		//This is handled in the constructor so it won't send failed emails just because the handle() method is empty
//		$this->sendFailedJobEmail();
	}
}