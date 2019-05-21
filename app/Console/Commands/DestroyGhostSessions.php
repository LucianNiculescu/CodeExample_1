<?php

namespace App\Console\Commands;

use App\Models\Radius\RadacctNew as RadacctNewModel;
use Illuminate\Console\Command;

class DestroyGhostSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:destroyGhostSessions {minutes=10} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '1. Cleaning up NULL Connectinfo_Start records; 2. Cleaning up NULL Connectinfo_Stop records and acctstoptime; 3. Removing records stale for x (default 10) minutes';

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
    	$minutes = $this->argument('minutes');

		//Step 1: Cleaning up NULL Connectinfo_Start records
		$step1 = RadacctNewModel::whereNull('connectinfo_start')->delete();
		//log the Step
		if(config('app.debug') && $step1)
			\Log::info('Cronjob: destroyGhostSessions:start Step 1 has been updated at'.\Carbon\Carbon::now());

		//Step 2: Cleaning up NULL Connectinfo_Stop records and acctstoptime
		$step2 = RadacctNewModel::whereNull('connectinfo_stop')->whereNull('acctstoptime')->delete();
		//log the Step
		if(config('app.debug') && $step2)
			\Log::info('Cronjob: destroyGhostSessions:start Step 2 has been updated at'.\Carbon\Carbon::now());

		//Step 3: Removing records stale for x hours
		$step3 = RadacctNewModel::where('lastseen', '<', \Carbon\Carbon::now()->subMinutes($minutes))
			->whereNull('acctstoptime')
			->update([
				'acctterminatecause' 	=> 'Orphaned-Session-Limit',
				'acctstoptime'			=> \Carbon\Carbon::now()->subMinutes($minutes)
			]);

		$step31 = RadacctNewModel::whereRaw('`radacct_new`.`lastseen` > `radacct_new`.`acctstoptime`')
			->where('lastseen', '>', \Carbon\Carbon::now()->subMinutes($minutes + 1))
			->update([
				'acctstoptime' => NULL
			]);
		//log the Step
		if(config('app.debug') && ($step3 || $step31))
			\Log::info('Cronjob: cron:destroyGhostSessions Step 3 has been updated at'.\Carbon\Carbon::now());
    }
}
