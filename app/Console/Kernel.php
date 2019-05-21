<?php

namespace App\Console;

use App\Console\Commands\ActivateVouchers;
use App\Console\Commands\ChangeScheduledSsids;
use App\Console\Commands\ClearHheClients;
use App\Console\Commands\ClearPasswordAttempts;
use App\Console\Commands\DeactivateVouchers;
use App\Console\Commands\DeleteExpiredClients;
use App\Console\Commands\DeleteOldTokens;
use App\Console\Commands\DeleteTransactionReceipts;
use App\Console\Commands\DeleteUselessTransactions;
use App\Console\Commands\DestroyGhostSessions;
use App\Console\Commands\MerakiCheck;
use App\Console\Commands\RemoveCsvFiles;
use App\Console\Commands\RevertScheduledSsids;
use App\Console\Commands\ShutdownIdle;
use App\Console\Commands\TranslationsBackup;
use App\Console\Commands\UpdateGender;
use App\Console\Commands\UpdatePmsDynamicIp;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
         Commands\TranslationsBackup::class,
         Commands\RemoveCsvFiles::class,
         Commands\DeleteOldTokens::class,
         Commands\DeleteUselessTransactions::class,
         Commands\DeleteExpiredClients::class,
         Commands\ActivateVouchers::class,
         Commands\DeactivateVouchers::class,
         Commands\ClearPasswordAttempts::class,
         Commands\ClearHheClients::class,
         Commands\MerakiCheck::class,
         Commands\DestroyGhostSessions::class,
         Commands\ShutdownIdle::class,
         Commands\UpdateGender::class,
         Commands\DeleteTransactionReceipts::class,
         Commands\RevertScheduledSsids::class,
         Commands\ChangeScheduledSsids::class,
         Commands\SetupElasticIndexes::class,
         Commands\UpdatePmsDynamicIp::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
	protected function schedule(Schedule $schedule)
	{
		//Runs daily, every 5 minutes from 03:00
		$schedule->command(TranslationsBackup::class)->dailyAt('03:00'); // Dispatching a Job that will create a CSV File with the translations
		$schedule->command(RemoveCsvFiles::class, ['1', '--queue'])->dailyAt('03:05'); // Remove CSV Files older than x months (default 1) from /var/www/public/admin/reports/csv
		$schedule->command(DeleteOldTokens::class, ['1'])->dailyAt('03:10'); // Deletes the tokens that are older than x (default 1) days
		$schedule->command(DeleteUselessTransactions::class, ['3', '1000'])->dailyAt('03:15'); //Delete a number of created transactions older than 3 months
		//$schedule->command(DeleteExpiredClients::class, ['1000'])->dailyAt('03:20'); // TODO: This comes from V1, why do we do it? Clear expired maestro clients

		//Runs every hour
		$schedule->command(ActivateVouchers::class)->hourly(); // Activate vouchers that are inactive and startDate < now()
		$schedule->command(DeactivateVouchers::class)->hourly(); // Deactivate vouchers that are active and stopDate < now()
		//$schedule->command(ClearPasswordAttempts::class, ['3'])->hourly(); // Update reminder table where there are more than 2 attempts and updated < last x (default 3) hours
		//$schedule->command(ClearHheClients::class)->hourly(); // TODO: This comes from V1, why do we do it? Clear expired hhe clients

		//Runs every 10 minutes
		//$schedule->command(MerakiCheck::class)->everyTenMinutes(); // Update airhealth.hardware based on a xml taken from a URL from site_attribute having "meraki_network"
		//$schedule->command(DestroyGhostSessions::class, ['10'])->everyTenMinutes(); // 1. Cleaning up NULL Connectinfo_Start records; 2. Cleaning up NULL Connectinfo_Stop records and acctstoptime; 3. Removing records stale for x (default 10) minutes
		//$schedule->command(ShutdownIdle::class, ['20'])->everyTenMinutes(); // Shuts down sessions older than x (default 20) minutes where IP is 192.168.1.2 by adding a acctstoptime | Might be CC only
		//$schedule->command(UpdateGender::class)->everyTenMinutes(); // Checks the name with the names that we have in our DB to return the gender
		$schedule->command(UpdatePmsDynamicIp::class)->everyTenMinutes(); // Updates the IP of site attributes that have dynamic_ip enabled (for UPMS and Captive PMS)

		//Runs every minute
		$schedule->command(DeleteTransactionReceipts::class, ['2', '10000'])->everyMinute(); // Delete a number of transaction receipts older than x (default 2) months

		// These are the SSID scheduling and was never implemented (unless it was)
//		$schedule->command(RevertScheduledSsids::class, ['2'])->everyMinute(); //THIS MUST BE CHECKED AS I THINK IT'S NOT CORRECT
//		$schedule->command(ChangeScheduledSsids::class, ['2'])->everyMinute(); //THIS MUST BE CHECKED AS I THINK IT'S NOT CORRECT
	}
}
