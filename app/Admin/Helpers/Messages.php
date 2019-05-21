<?php
/**
 * Created by PhpStorm.
 * User: sheri
 * Date: 7/1/2016
 * Time: 02:50 PM
 */

namespace App\Admin\Helpers;

use \App\Models\AirConnect\Message as MessageModel;

class Messages
{

	const ERROR_MSG 	= 'error';
	const WARNING_MSG 	= 'warning';
	const SUCCESS_MSG 	= 'success';

	/**
	 * Show Message
	 * Add a message to the DB and then show it to the user
	 * @param $type
	 * @param $description
	 * @param bool $addToSession
	 * @param bool $addToDB
	 */
	public static function create( $type, $description, $addToSession = true, $addToDB = true )
	{

		if( $addToDB )
		{
			// saving message to airconnect.message table
			self::toDB($type, $description);
		}

		if ($addToSession)
		{
			// saving message to flash session
			self::toSession($type, $description);
		}

	}

	/**
	 * Saving message to the flash session
	 * @param $type
	 * @param $description
	 */
	public static function toSession($type, $description)
	{
		// Retrieving messages session
		$messages = session('messages');
		$messages[] = [$type,$description];
		\Session::put('messages', $messages );
	}

	/**
	 * Saving Message to DB
	 * @param $type
	 * @param $description
	 */
	public static function toDB($type, $description)
	{
		// Set the vars
		$userId = 0;
		$roleId = 999;

		$siteId = session('admin.site.loggedin');

		if(is_null($siteId))
			$siteId = session('admin.user.site');

		if( !$siteId ) $siteId = 0;

		// Test if we have a user logged in
		if ( \Auth::check() )
		{
			$userId = \Auth::user()->id;
			$roleId = \Auth::user()->role_id;	// was adminId
		}

		// Add the message to the DB
		$message = new MessageModel();
		$message->site = $siteId;
		$message->type = $type;
		$message->description = $description;
		$message->user_id = $userId;
		$message->role_id = $roleId;
		$message->save();
	}

}