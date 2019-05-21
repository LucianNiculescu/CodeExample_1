<?php

namespace App\Console\Commands;

use App\Models\AirConnect\SsidSchedule as SsidScheduleModel;
use Illuminate\Console\Command;

class RevertScheduledSsids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:revertScheduledSsids {hours=2} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Get all the scheduled SSIDs with 'active' or 'changed' status from this hour and x hours before and change the status to 'inactive'";

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
	 * Revert scheduled SSIDs
	 * Get all the scheduled SSIDs from this hour and 2 hours before (past hours in case the cron has issues)
	 * Only get 'active' or 'changed' SSIDs so we don't try to change twice
	 * TODO: Call the API and change the SSID
	 * Set the status to 'inactive' so we know what we have done and as we have reverted we have finished this schedule
     */
    public function handle()
    {
    	$hours = $this->argument('hours');
		$records = SsidScheduleModel::where('status', 'active')
			->orWhere('status', 'changed')
			->whereBetween('restore_date', [\Carbon\Carbon::now()->subHours($hours), \Carbon\Carbon::create(null, null, null, null, 0, 0) ])
			->update(['status' => 'inactive']);

		//log the cronjob
		if(config('app.debug') && $records)
			\Log::info("Cronjob: cron:revertScheduledSsids have been updated at ".\Carbon\Carbon::now());
    }
}
