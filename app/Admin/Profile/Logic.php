<?php

namespace App\Admin\Profile;

use App\Models\AirConnect\Admin;
use App\Admin\Helpers\Messages;

class Logic
{
	public static function saveProfileInfo($request){

	    //dd($request);
		// if password is not empty
		if(!empty($request->profile_password)){
			//update the password
			Admin::where('username', session('admin.user.username'))
				->update(['password' => \Hash::make($request->profile_password)]);
		}

		// if language chosen is different from the language in session and is not empty
		if($request->profile_language != session('admin.user.language') && !empty($request->profile_language)){
			// update language in admin table
			Admin::where('username', session('admin.user.username'))
				->update(['language' => $request->profile_language]);
			// set the new language in the session
			$request->session()->put('admin.user.language', $request->profile_language);
		}
		// if timezone chosen is different from the timezone in session and is not empty
		if($request->profile_timezone != session('admin.user.timezone') && !empty($request->profile_timezone)){
			// update timezone in admin table
			Admin::where('username', session('admin.user.username'))
				->update(['timezone' => $request->profile_timezone]);
			// set the new timezone in the session
			$request->session()->put('admin.user.timezone', $request->profile_timezone);
		}

        Messages::create(Messages::SUCCESS_MSG, trans('admin.profile-saved'));
		return \Redirect::back();
	}
}