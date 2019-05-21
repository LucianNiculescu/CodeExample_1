<?php

namespace App\Admin\Helpers\Composers;

/**
 * Not used anymore
 * Class MapDataComposer
 * @package App\Admin\Helpers\Composers
 */
class MapDataComposer
{

	public function compose($view)
	{
		// gets the current siteId
		$siteId = session('admin.site.loggedin');

		// get gateways and hardware data to plot on the map
		$plotData = $this->getPlotData($siteId);
		$view->with('locationsString', $plotData);

	}


}