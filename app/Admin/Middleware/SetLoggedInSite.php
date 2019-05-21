<?php

namespace App\Admin\Middleware;

use App\Admin\Modules\Sites\Services\CachedSiteService;
use Illuminate\Http\Request;

/**
 * HTTP Middleware to set the logged in Site into session via route parameters
 *
 * Registered for use with middleware `set_logged_in_site`
 */
class SetLoggedInSite
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
	public function handle(Request $request, \Closure $next)
	{
		if(!is_null($request->siteId) && is_numeric($request->siteId))
			session()->put('admin.site.loggedin', $request->siteId);

		return $next($request);
	}
}