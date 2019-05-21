<?php

namespace App\Admin\Middleware;

use App\Helpers\Language;
use Closure;

class Locale
{
	/**
	 * Handle an incoming request.
	 * Gets the permission automatically from the URLPath
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
    public function handle($request, Closure $next)
    {
		// Setting the Locale to the user's language
		\App::setLocale(Language::getLanguage(true));
		return $next($request);
    }
}
