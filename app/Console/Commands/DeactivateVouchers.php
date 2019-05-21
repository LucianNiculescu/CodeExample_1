<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AirConnect\Voucher as VoucherModel;

class DeactivateVouchers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:deactivateVouchers {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate vouchers that are active and stopDate < now()';

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
		$records = VoucherModel::where('status', 'active')
			->where('stop', '<', \Carbon\Carbon::now())
			->update(['status' => 'expired']);

		//log the cronjob
		if(config('app.debug') && $records)
			\Log::info("Cronjob: cron:deactivateVouchers have been updated at ".\Carbon\Carbon::now());
    }
}
