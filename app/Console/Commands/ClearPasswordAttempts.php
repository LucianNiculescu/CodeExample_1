<?php

namespace App\Console\Commands;

use App\Models\AirConnect\Reminder as ReminderModel;
use Illuminate\Console\Command;

class ClearPasswordAttempts extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cron:clearPasswordAttempts {hours=3} {--queue}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update reminder table where there are more than 2 attempts and updated < last x (default 3) hours';

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$hours  = $this->argument('hours');
		$records = ReminderModel::where('updated', '<', \Carbon\Carbon::now()->subHours($hours))
			->where('attempts', '>','2')
			->update(['attempts' => '0']);

		//log the cronjob
		if(config('app.debug') && $records)
			\Log::info("Cronjob: cron:clearPasswordAttempts have been updated at ".\Carbon\Carbon::now());
	}
}
