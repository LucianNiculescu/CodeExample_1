<?php


namespace App\Admin\Modules\Passwords;

use App\Admin\Modules\Passwords\Logic as Passwords;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use \App\Admin\Helpers\Messages;

class Controller extends BaseController
{
    /**
     * Show forgot password form
     */
    public function forgot()
    {
        // if the user is already logged in they will be routed to estate page
        if (Auth::check())
        {
            return redirect()->route('estate');
        }
        // otherwise go to forgot password page
        return view('admin.modules.passwords.forgot');
    }

    /**
     * Reset Password
     * Gets the form request data and passes it to the reset password function in Passwords/Logic
     * @return bool
     */
    public function reset()
    {
        $data = \Request::all();
        return Passwords::resetPassword($data);

    }

    /**
     * Change Password
     * Typehints the request data. Gets the token section of the URL using $request->input().Passes the token to
     * VerifyToken in Passwords/Logic. If token returns false and error and redirect are thrown. If true returns
     * change password form view with the token and users email
     * @param Request $request
     * @return mixed
     */
    public function change(Request $request)
    {
        // if the user is already logged in they will be routed to estate page
        if (Auth::check())
        {
            return redirect()->route('estate');
        }

        //get token from URL
        $token = $request->input('token');

        // verify token in logic file
        $getAccount = Passwords::verifyToken($token);

        // if token is not verified, redirect to password forgot page and show token error
        if($getAccount == false){
            Messages::create(Messages::ERROR_MSG, trans('admin.error-cant-find-token'));
            return redirect('/password/forgot');
        }

        return view('admin.modules.passwords.change', ['token' => $token, 'email' => $getAccount]);

    }

    /**
     * Save Password
     * Gets the request data from form, passes it to the Passwords/Logic to save after validation
     * @param Request $request
     * @return mixed
     */
    public function save(Request $request)
    {
        return Passwords::savePassword($request);
    }

}