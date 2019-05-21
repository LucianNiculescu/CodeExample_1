<?php

namespace App\Console\Commands;

use App\Models\Radius\RadacctNew as RadacctNewModel;
use Illuminate\Console\Command;

class ShutdownIdle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:shutdownIdle {minutes=20} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shuts down sessions older than x (default 20) minutes where IP is 192.168.1.2 by adding a acctstoptime';

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

		$records = RadacctNewModel::where('lastseen', '<', \Carbon\Carbon::now()->subMinutes($minutes))
			->where('nasipaddress', '192.168.1.2')
			->whereNull('acctstoptime')
			->update([
				'acctterminatecause' 	=> 'Orphaned-Session-20Mins',
				'acctstoptime'			=> \Carbon\Carbon::now()->subMinutes($minutes)
			]);

		//log the cronjob
		if(config('app.debug') && $records)
			\Log::info('Cronjob: cron:shutdownIdle have been updated at'.\Carbon\Carbon::now());
    }
}
