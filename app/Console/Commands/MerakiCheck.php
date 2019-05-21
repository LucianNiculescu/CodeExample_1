<?php

namespace App\Console\Commands;

use App\Models\AirConnect\SiteAttribute as SiteAttributeModel;
use App\Models\AirHealth\Hardware as AirHealthHardwareModel;
use Illuminate\Console\Command;

class MerakiCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:merakiCheck {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update airhealth.hardware based on a xml taken from a URL from site_attribute having "meraki_network"';

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
		//Get the site attributes with name = meraki_network
		$siteAttributes = SiteAttributeModel::where('name', 'meraki_network')->get();
		$update = [];
		if($siteAttributes->count() > 0) {
			//Download the XML and set some attributes for the update of the airhealth hardware
			foreach($siteAttributes as $siteAttr) {
				$siteId = $siteAttr->id;
				$url 	= $siteAttr->value;
				$sXML 	= \App\Helpers\UrlHelper::downloadPage($url);
				$oXML 	= new \SimpleXMLElement($sXML);
				if(!empty($oXML)) {
					$nasId 	= $oXML->name;
					$status	= 'active';
					$type 	= 'CISCOMERAKI';

					if(!empty($oXML->node)) {
						$mac = $oXML->node->mac;
						$device = $oXML->node->name;
						$lat = $oXML->node->lat;
						$lng = $oXML->node->lng;
						$location = $lat . "," . $lng;
						$extip = $oXML->node->last_reported_from;
						$active = $oXML->node->is_active;

						//Creating the array for bulk update
						$update[] = [
							'alert' 	=> 0,
							'ip' 		=> $extip,
							'site'		=> $siteId,
							'status'	=> $status,
							'type' 		=> $type,
							'nasid' 	=> $nasId,
							'location' 	=> $location,
							'name'		=> $device,
							'updated' 	=> (($active == 'true')?  \Carbon\Carbon::now():''),
							'mac' 		=> $mac
						];
					}
				}
			}
			if(!empty($update)) {
				foreach($update as $key => $val) {
					//unset the mac value and use it in the where condition
					$mac = $val['mac'];
					unset($update[$key]['mac']);
					//update the records that have the mac address
					try {
						AirHealthHardwareModel::where('mac', $mac)->update($val);
					} catch (\Exception $e) {
						return false;
					}

				}
				\Log::info('Cronjob: cron:merakiCheck has been updated at'.\Carbon\Carbon::now());
			}
		}
    }
}
