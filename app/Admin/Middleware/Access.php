<?php

namespace App\Admin\Middleware;

use Closure;
use App\Admin\Helpers\Messages;

class Access
{
	/**
	 * Handle an incoming request.
	 * Gets the permission automatically from the URLPath
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function handle($request, Closure $next)
	{
		// If logged in
		if(auth()->check())
		{
			// If Dev
			if (auth()->user()->role_id == 0)	// was adminId
				// Continue...
				return $next($request);

			// Create the permission from the path
			$permission = self::permissionFromPath( $request->path() );

			// Checking if user is logged in and has the Permission
			if (auth()->user()->hasPermission($permission) or auth()->user()->hasPermission('all-'.$permission))
				// Continue...
				return $next($request);
		}

		// Send an error
		Messages::create( Messages::ERROR_MSG, trans('error.unauthorized') .': /' .$request->path() );

		// Redirects to the root
		return redirect('/admin');
	}


	/**
	 * Take in a path and output the correct permission for that path
	 * @param $path
	 * @return mixed
	 * NB. To build a route add URL, then number variable, then other variables and lastly action (edit or create)
	 */
	public static function permissionFromPath( $path )
	{
		// Basic permissions are path but dot separated
		$permission = '';

		// Trim first / if there is one
		$path = ltrim($path, '/');

		// Exploding the permission into an array
		$parts = explode('/', $path);

		// Loop into the parts
		$idFlag = false;
		foreach ($parts as $key => $part)
		{
			// If the part is numeric or has : or @ for mac and emails
			if( is_numeric($part) or strpos($part, ':') !== false or strpos($part, '@') !== false )
				$idFlag = true; // Set the flag numeric = true

			// If the flag numeric is true
			if( $idFlag == true && $part != 'edit' && $part != 'create' )
				// Remove the part unless it is 'edit' or 'create'
				unset($parts[$key]);

			// If the part is 'datatable', remove
			if( isset($parts[$key]) && in_array($part, ['datatable', 'json']))
				unset($parts[$key]);
		}

		// Count the parts
		$partsCount = count($parts);

		// If the count is > 3
		if( $partsCount > 3 )
		{
			// Concat 0, 1, 2
			$permission .= $parts[0] .'.' .$parts[1] .'.' .$parts[2];

			// Unset the first 3 parts
			unset($parts[0]);
			unset($parts[1]);
			unset($parts[2]);

			// If wer still have an array of parts
			if( is_array($parts) )
				// Implode rest of the parts with '-'
				$permission .= '-' .implode('-', $parts);
		}
		else
		{
			// Implode with dots '.'
			$permission .= implode('.', $parts);;
		}

		// Return the permission
		return $permission;
	}
}
