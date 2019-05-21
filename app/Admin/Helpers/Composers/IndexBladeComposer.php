<?php

namespace App\Admin\Helpers\Composers;


class IndexBladeComposer {

    /**
     * Setting up the $data array with createUrl and createAccess variable to be sent to the view
     * @param $view
     */
    public function compose($view)
    {
       	$path 		= \Request::path();

		// Exception for estate page for now
		if($path == 'estate')
			$path = 'manage/sites';

        $createUrl 		= '/' . $path .'/create';
        $createAccess 	= str_replace('/', '.', $path) .'.create';

        $data =[
            'createUrl'     =>  $createUrl,
            'createAccess'  =>  $createAccess ,
        ];

        $view->with($data);

    }
}