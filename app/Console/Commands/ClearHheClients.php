<?php

namespace App\Console\Commands;

use App\Models\HHE\Network as HheNetworkModel;
use Illuminate\Console\Command;

class ClearHheClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:clearHheClients {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expired hhe clients';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
	 * TODO: Localisation
     */
    public function handle()
    {
		$records = HheNetworkModel::where('status', 'deployed')
			->where('expiry', '<', \Carbon\Carbon::create(null, null, null, 12, 0, 0))
			->update(['status' => 'created', 'expiry' => '0000-00-00 00:00:00']);

		//log the cronjob
		if(config('app.debug') && $records)
			\Log::info("Cronjob: cron:clearHheClients have been updated at ".\Carbon\Carbon::now());
    }
}
