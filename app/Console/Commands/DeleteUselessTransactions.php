<?php

namespace App\Console\Commands;

use App\Models\AirConnect\Transaction as TransactionModel;
use Illuminate\Console\Command;

class DeleteUselessTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:deleteUselessTransactions {months=3} {limit=1000} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a number of transaction receipts';

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
		$months = $this->argument('months');
		$limit = $this->argument('limit');

    	$records = TransactionModel::where('created', '<', \Carbon\Carbon::now()->subMonths($months))
			->where('status', 'Created')
			->orderBy('created', 'asc')
			->limit($limit)
			->delete();

		//log the cronjob
		if(config('app.debug') && $records)
			\Log::info("Cronjob: cron:deleteUselessTransactions have been updated at ".\Carbon\Carbon::now());
    }
}
