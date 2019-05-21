<?php

namespace App\Admin\Middleware;

use App\Admin\Helpers\Messages;
use Closure;

/**
 * HTTP Middleware to ensure a Site is stored in session
 * Redirects user to Estate
 *
 * Registered for use with middleware `require_site`
 */
class LoggedInSiteRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	if(logged_in_site() === false)
		{
			Messages::create( Messages::ERROR_MSG, trans('admin.select-a-site'));
			return redirect()->route('estate');
		}

        return $next($request);
    }
}
