<?php

namespace App\Admin\Helpers\Composers;


class GlobalInfoComposer {

    /**
     * Gets the correct list of widgets for the user
     * @param $view
     */
    public function compose($view)
    {
		$routeName = \Request::route()->getName();
		// If route has a . then get all text after the .
		$routeName = substr($routeName, strpos($routeName, '.') !== false ? strpos($routeName, '.') +1 : 0);

		$view->with('bodyClasses', self::getBodyClasses());

        // sets the jsConstant variable in the master blade to be referenced in the JSON
        $view->with('jsConstants', [
            'site_id'   => session('admin.site.loggedin'),
            'user_id'   => session('admin.user.id'),
            'route'     => $routeName,
			'site_type' => session('admin.site.type'),
			'role_id' 	=> session('admin.user.role_id'),
			'lang' 		=> session('admin.user.language', 'en')
        ]);

        // Permission to see this - only Dev
        if( \Gate::allows('access', 'git-hash') )
			$view->with([
				'gitHash' 		=> exec('git rev-parse --short HEAD'),
				'gitFullHash' 	=> exec('git rev-parse HEAD')
			]);
    }

	/**
	 * Gets the classes for the HTML body tag
	 * @return string
	 */
    private function getBodyClasses()
	{
		$url = \Request::segments();
		$bodyClasses = '';
		foreach($url as $segment)
			if(!is_numeric($segment))
				$bodyClasses .= ' ' . $segment;

		return $bodyClasses;
	}


}