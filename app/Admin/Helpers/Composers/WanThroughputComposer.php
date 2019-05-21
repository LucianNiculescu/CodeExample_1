<?php

namespace App\Admin\Helpers\Composers;

use App\Models\AirConnect\Gateway;

class WanThroughputComposer {

    public function compose($view)
    {

		$gateways = Gateway::where(['site' => session('admin.site.loggedin'), 'status' => 'active'])->get();
		$view->with('gateways', $gateways);
    }

}