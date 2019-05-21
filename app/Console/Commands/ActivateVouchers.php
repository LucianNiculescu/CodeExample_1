<?php

namespace App\Console\Commands;

use App\Models\AirConnect\Voucher as VoucherModel;
use Illuminate\Console\Command;

class ActivateVouchers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:activateVouchers {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate vouchers that are inactive and startDate < now()';

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
		$records = VoucherModel::where('status', 'inactive')
			->where('start', '<', \Carbon\Carbon::now())
			->update(['status' => 'active']);

		//log the cronjob
		if(config('app.debug') && $records)
			\Log::info("Cronjob: cron:activateVouchers have been updated at ".\Carbon\Carbon::now());
    }
}
