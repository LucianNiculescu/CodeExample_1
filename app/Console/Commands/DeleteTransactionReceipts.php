<?php

namespace App\Console\Commands;

use App\Models\AirConnect\TransactionReceipt as TransactionReceiptModel;
use Illuminate\Console\Command;

class DeleteTransactionReceipts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:deleteTransactionReceipts {months=2} {limit=10000} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a number of transaction receipts older than x (default 2) months';

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
		$limit 	= $this->argument('limit');

		$records = TransactionReceiptModel::where('created', '<', \Carbon\Carbon::now()->subMonths($months))
			->orderBy('created', 'asc')
			->limit($limit)
			->delete();

		//log the cronjob
		if(config('app.debug') && $records)
			\Log::info("Cronjob: cron:deleteTransactionReceipts have been updated at ".\Carbon\Carbon::now());
    }
}
