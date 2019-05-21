<?php
namespace App\Transformers;

use App\Models\AirConnect\Blocked;
use App\Helpers\DateTime;

class BlockedTransformer extends BaseTransformer
{
	/**
	 * Transform a blocked record for use with a datatable
	 *
	 * @param Blocked $blocked
	 * @return array
	 */
	public function datatablesFormat(Blocked $blocked)
	{
		return [
			'id' 		=> $blocked->id,
			'site' 		=> $blocked->parentSite->name,
			'mac' 		=> strtoupper($blocked->mac),
			'reason' 	=> $blocked->reason,
			'blocker' 	=> $blocked->admin->username,
			'created' 	=> DateTime::medium($blocked->created, true)
		];
	}
}