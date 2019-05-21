<?php

namespace App\Admin\Helpers\Composers;


use App\Models\AirConnect\Gateway;


class LatencyComposer {

    public function compose($view)
    {

		$macs = Gateway::where(['site' => session('admin.site.loggedin'), 'status' => 'active'])->get();
		$view->with('macs', $macs);
    }

}