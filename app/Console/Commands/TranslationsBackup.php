<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\TranslationsBackupJob;

class TranslationsBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:translationsBackup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatching a Job that will create a CSV File with the translations';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Backup Translations into a CSV file in /public/uploads/reports/translations, sends Email to applications@airangel.com and remove other translations older than 7 days
     */
    public function handle()
    {
		if(config('app.translations.backup'))
			dispatch(new TranslationsBackupJob);
    }
}
