<?php
namespace App\Admin\Profile;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Admin\Profile\Logic as Profile;

class Controller extends BaseController
{
	public function saveProfileInfo(Request $request){
		return Profile::saveProfileInfo($request);
	}

}