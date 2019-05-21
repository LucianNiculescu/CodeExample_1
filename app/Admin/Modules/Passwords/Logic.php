<?php

namespace App\Admin\Modules\Passwords;

use App\Admin\Helpers\Messages;
use App\Models\AirConnect\Admin;
use App\Models\AirConnect\Content;
use App\Models\AirConnect\Token;
use App\Helpers\Language;
use Carbon\Carbon;
use App\Jobs\EmailJob;


class Logic
{
    /**
     * Reset Password
     * Gets the data from the form. Gets the email address and searches the admin for users with that email.
     * Gives error if there are no users and redirects back. If user is found, creates a token and updates token table.
     * If token already exists for this user email address it overwrites the current token. Sets the subject, gets the
     * browser language, gets the content for the email from the content table and runs the string replace on it before
     * sending the email  and returning a message on success for the user
     * @param $data
     * @return bool
     */
    public static function resetPassword($data)
    {
        $email = $data['email'];
        $user = Admin::where('username', $email)->first();
        if (empty($user)){

            Messages::create(Messages::ERROR_MSG, trans('admin.email-password-reset-no-account', ['email' => $email]));
            return \Redirect::back();

        }

        //gets random 6 char string
        $token = str_random(6);

        //checks the token table for email. If exists updates the token field. If not creates new one
        Token::updateOrCreate(
            ['email' => $email],
            ['token' => $token, 'created' => Carbon::now()]
        );

        $subject = trans('admin.password-reset');

        // get browser language
        $language = Language::getLanguage();

        // Get the first content for the password reset
        $resetPasswordContent = Content::where(['name' => 'email_password_reset', 'language' => $language])->first();

        // If there is no content
        if( is_null($resetPasswordContent ))
			$resetPasswordContent = trans('admin.email-password-reset');
        else
			$resetPasswordContent = $resetPasswordContent->value;

        $resetPasswordContent = self::emailStringReplace($resetPasswordContent, $token);

        $tags = array($subject, 'site-' . $user->site);

        //Set the EmailJob, create the success message and redirect to login page
		dispatch(new EmailJob(null, $email, $subject, $resetPasswordContent, null, $tags));

		Messages::create(Messages::SUCCESS_MSG, trans('admin.email-password-link-sent', ['email' => $email]));
		return redirect('/login');

    }

    /**
     * Save Password
     * Gets request data from for passed in, checks the password and confirm password match. If matched, save new password
     * as a hashed password to the Admin table and send the user back to the login page with a success message
     * @param $request
     * @return mixed
     */
    public static function savePassword($request)
    {
        // if passwords match TODO: make sure password fields are not empty
        if($request->confirm_password == $request->password){
            // update the password (hashed) for the Admin user
            Admin::where('username', $request->email)
                ->update(['password' => \Hash::make($request->password)]);

            //return a message to the user to tell them
            Messages::create(Messages::SUCCESS_MSG, 'Password saved, please log in with your shiny new password');
        }else{

            //return a message to the user to tell them
            Messages::create(Messages::ERROR_MSG, 'Password save fail');
        }

        //redirect to login page
        return redirect('/login');

    }

    /**
     * Verify Token
     * Uses passed in token to check it exists in the token table. If doesnt exist returns false. If does exist, gets
     * the time now and the time the token was created and parse it with Carbon. Figures out the difference using the
     * diffInHours carbon function and returns false if token is over 24 hours old. Returns the users email if token
     * is under 24 hours old to be used in the new password form
     * @param $token
     * @return bool
     */
    public static function verifyToken($token)
    {
        // check the token db table for the token
        $result = Token::where(['token' => $token])->first();

        // if token doesnt match, return invalid
        if(empty($result)){
            return false;
        }

        // get the current timestamp using Carbon
        $now = Carbon::now();

        // get date created and turn it into a Carbon date
        $createdDate = Carbon::parse($result->created);

        // use carbon to get the difference in hours between the two dates
        $difference = $now->diffInHours($createdDate);

        // if difference is over 24 hours, return invalid
        if($difference >= 24)
            return false;

        // if less than 24 hours, return valid
        return $result->email;

    }

    /**
     * String replace for email_password_reset
     * This is the string replace for the value blob found in the airconnect.content table with
     * the name email_password_reset. This basically replaces %%link%% with the url that is
     * emailed to the user when they use the forgot password form
     * @param $content
     * @param $token
     * @return mixed
     */
    private static function emailStringReplace($content, $token)
    {
        $url = \URL::to('/'); //'http://local.myairangel.co.uk/';
        // Key is what we want to change, value is what we are changing to
        $replace = [
            '%%link%%'   => $url . '/password/change?token='. $token,
        ];

        // Do the replace
        $content = str_ireplace(array_keys($replace), array_values($replace), $content);
        return $content;
    }
}



