<?php

namespace App\Console\Commands;

use App\Models\AirConnect\SsidSchedule as SsidScheduleModel;
use Illuminate\Console\Command;

class ChangeScheduledSsids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:changeScheduledSsids {hours=2} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all the scheduled SSIDs from this hour and x hours before (past hours in case the cron has issues)';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
	 *
	 * Get all the scheduled SSIDs from this hour and 2 hours before (past hours in case the cron has issues)
	 * Only get 'active' SSIDs so we don't try to change twice
	 * TODO: Call the API and change the SSID
	 * Set the status to 'changed' so we know what we have done
	 *
     */
    public function handle()
    {
		$hours = $this->argument('hours');
		$records = SsidScheduleModel::where('status', 'active')
			->whereBetween('change_date', [\Carbon\Carbon::now()->subHours($hours), \Carbon\Carbon::create(null, null, null, null, 0, 0)])
			->update(['status' => 'changed']);

		//log the cronjob
		if(config('app.debug') && $records)
			\Log::info("Cronjob: cron:changeScheduledSsids have been updated at ".\Carbon\Carbon::now());
    }
}
