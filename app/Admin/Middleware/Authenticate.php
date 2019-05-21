<?php

namespace App\Admin\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use \App\Admin\Modules\Sites\Logic as Sites;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('login')->withErrors(trans('error.unauthorized'));
            }
        }

        if(Auth::check()){

			// setting up estate and children session
			if(!session()->has('admin.site.estate') || !session()->has('admin.site.children'))
			{
				// Use the logged in Site, or the User's Site if not logged into one
				$cachedSite = cached_site_service()->loggedInCachedSite() !== false
								? cached_site_service()->loggedInCachedSite()
								: cached_site_service()->userCachedSite();

				// Set the admin.site.children variable, which is the estate of the logged in site (or User's Site)
				session( ['admin.site.children' => $cachedSite->estate()->pluck('id')->toArray() ] );

				// Set the admin.site.estate variable, which is the estate of the User's Site
				session( ['admin.site.estate'   => user_estate()->pluck('id')->toArray() ]);
			}
		}

        return $next($request);
    }
}
