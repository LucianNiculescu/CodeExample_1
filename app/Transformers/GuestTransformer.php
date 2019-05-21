<?php

namespace App\Transformers;

use App\Models\AirConnect\User;

/**
 * Class responsible for transforming a User object (as a Guest)
 */
class GuestTransformer extends BaseTransformer
{
	/**
	 * Format a Guest record for sending with Nodin
	 *
	 * @param  User $user
	 * @return array
	 */
	public function nodinFormat(User $user)
	{
		// Ensure we have the attributes loaded
		$user->load('attributes');

		// Get the Guest data (including the attributes relation) into an array
		$data = $user->toArray();

		// Unset data we don't want to include
		unset($data['password']);
		unset($data['site']);

		// Return the data
		return $data;
	}
}