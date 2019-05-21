<?php
namespace App\Transformers;

use App\Models\AirConnect\Whitelist;
use App\Helpers\DateTime;

class WhitelistTransformer extends BaseTransformer
{
	/**
	 * Transform a blocked record for use with a datatable
	 *
	 * @param Whitelist $whitelist
	 * @return array
	 */
	public function datatablesFormat(Whitelist $whitelist)
	{
		return [
			'id' 			=> $whitelist->id,
			'site' 			=> $whitelist->parentSite->name,
			'mac' 			=> strtoupper($whitelist->mac),
			'description' 	=> $whitelist->description,
			'performedby' 	=> $whitelist->admin->username,
			'created' 		=> DateTime::medium($whitelist->created, true)
		];
	}
}
