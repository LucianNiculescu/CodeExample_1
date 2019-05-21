<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemoveCsvFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:removeCsvFiles {months=1} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove CSV Files older than x months (default 1) from /var/www/public/admin/reports/csv';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Remove CSV Files older than 30 days from /var/www/public/admin/reports/csv
	 * param int $months number of months when files older than will be deleted
     */
    public function handle()
    {
		$months = $this->argument('months');

		//get all the files from that folder
		$files = \File::files(public_path().'/uploads/reports/csv');
		if(!empty($files)) {
			foreach($files as $file) {
				//check if they are csv files
				if(\File::extension($file) == 'csv') {
					//set the date in the past that we don't want files older that this
					$prevDate = \Carbon\Carbon::now()->subMonths($months)->timestamp; //if we want to test the date we can use \Carbon\Carbon::now()->subMonths(1)->toDateTimeString();
					//get the files created/modified date
					$lastModified = \File::lastModified($file);
					//if the date that the file has been created is less than our date, delete the file
					if($lastModified < $prevDate) {
						\File::delete($file);
						if(config('app.debug'))
							\Log::info("Cronjob: cron:removeCsvFiles File {$file} has been deleted at ".\Carbon\Carbon::now());
					}
				}
			}
		}
    }
}
