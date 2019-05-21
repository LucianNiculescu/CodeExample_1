<?php

namespace App\Console\Commands;

use App\Models\AirConnect\SiteAttribute as SiteAttributeModel;
use App\Models\AirHealth\Hardware as HardwareModel;
use Illuminate\Console\Command;

class UpdatePmsDynamicIp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:updatePmsDynamicIp {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the PMS site attribute IP for the ones that have dynamic_ip enabled';

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

    	//Initialise the variables that are used
    	$siteAttributes = $ip = $name = $hardware = '';
    	$records = null;

    	//Get all the site attributes that have dynamic_ip enabled
		$dynamicIps = SiteAttributeModel::where(['name' => 'dynamic_ip', 'value' => 'true'])->get()->toArray();

		//Loop through them and get all the attributes that have the ip
		foreach($dynamicIps as $pmsAttr) {
			//Get all the site attributes for that site
			$siteAttributes = SiteAttributeModel::where(['ids' => $pmsAttr['ids']])->get();
			//Loop through site attributes and search for ip or uri
			foreach($siteAttributes as $siteAttr) {
				//Check if the name is URI or IP
				if($siteAttr->name == 'uri' || $siteAttr->name == 'ip') {
					$ip = $siteAttr->value;
					$name = $siteAttr->name;
				}
				//Get the value of airhealth.hardware so we can check if the ip has been changed
				if($siteAttr->name == 'dynamic_gateway_id')
					$hardware = $siteAttr->value;
			}

			//Check if we have the ip and hardware
			if(!empty($ip) && !empty($hardware)) {
				//Get the airhealth.hardware
				$hardware = HardwareModel::find($hardware);
				//Do something only when there is a hardware ip
				if(!empty($hardware->ip)) {
					//Check if the hardware is formed by multiple IPs
					$firstHardwareIp = explode(',', $hardware->ip);
					//Check if the ip of the site attribute is different as the airhealth.hardware's ip
					if($firstHardwareIp[0] !== $ip)
						//Update the site attribute with the new ip from airhealth.hardware
						$records = SiteAttributeModel::where([
							'ids' 	=> $pmsAttr['ids'],
							'name'	=> $name
						])->update(['value' => $hardware->ip]);
				}

			}
			//Reset variables
			$ip = $name = $hardware = '';
		}

		//log the cronjob
		if(config('app.debug') && $records)
			\Log::info("Cronjob: cron:updatePmsDynamicIp have been updated at ".\Carbon\Carbon::now());
    }
}
