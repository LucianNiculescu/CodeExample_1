<?php

namespace App\Console\Commands;

use App\Models\AirConnect\Token as TokenModel;
use Illuminate\Console\Command;

class DeleteOldTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:deleteOldTokens {days=1} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes the tokens that are older than x (default 1) days';

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
		$days = $this->argument('days');

		$records = TokenModel::where('created', '<', \Carbon\Carbon::now()->subDays($days))
			->delete();

		//log the cronjob
		if(config('app.debug') && $records)
			\Log::info("Cronjob: cron:deleteOldTokens have been updated at ".\Carbon\Carbon::now());
    }
}
