<?php

namespace App\Console\Commands;

use App\Models\Maestro\Client as MaestroClientModel;
use Illuminate\Console\Command;

class DeleteExpiredClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:deleteExpiredClients {limit=1000} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expired maestro clients';

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
		$limit = $this->argument('limit');

		$records = MaestroClientModel::where('DepartureDate', '<', \Carbon\Carbon::now()->toDateString())
			->limit($limit)
			->delete();

		//log the cronjob
		if(config('app.debug') && $records)
			\Log::info("Cronjob: cron:deleteExpiredClients have been updated at ".\Carbon\Carbon::now());
    }
}
