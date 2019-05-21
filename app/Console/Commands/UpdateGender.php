<?php

namespace App\Console\Commands;

use App\Admin\Helpers\HumanReadable;
use App\Models\AirangelTools\Processor as ProcessorModel;
use App\Models\AirConnect\User as UserModel;
use App\Models\AirConnect\UserAttribute as UserAttributeModel;
use Illuminate\Console\Command;

class UpdateGender extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:updateGender {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the name with the names that we have in our DB to return the gender';

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
		// Get the last processed User
		$lastUserId = ProcessorModel::select('value')->where('keyfield', 'genderid')->get()->first();
		if(empty($lastUserId))
			$value = 0;
		else
			$value = $lastUserId->value;

		//Set how many records are updated (default: 10k)
		$to = $value + 1;
		$from = $value + 10000;

		//Get the users having id between last id and +10k
		$users = UserModel::select('id','name')
			->whereBetween('id', [$to, $from])
			->where('name', '!=', '')
			->get();

		if($users->count() > 0) {
			$usersBatch = [];
			foreach($users as $user) {
				$forename = explode(" ", $user->name)[0];
				$gender = HumanReadable::getGender($forename);
				$usersBatch[] = [
					'ids' => $user->id,
					'name' => 'name-gender',
					'value' => $gender,
					'status' => 'active',
					'created' => \Carbon\Carbon::now()
				];
				$value = $user->id;
			}

			//Update user_attributes with the gender
			UserAttributeModel::insert($usersBatch);
			//Update processor table with the last updated user id
			ProcessorModel::where('keyfield', 'genderid')->update(['value' => $value]);

			//Log the CronJob
			\Log::info('Cronjob: cron:updateGender have been updated at '.\Carbon\Carbon::now(). ' and updated '.$users->count().' records.');
		}
    }
}
