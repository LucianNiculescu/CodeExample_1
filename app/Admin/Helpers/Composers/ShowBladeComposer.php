<?php

namespace App\Admin\Helpers\Composers;


class ShowBladeComposer {

    /**
     * Setting up the $data array with editUrl and editAccess variable to be sent to the view
     * @param $view
     */
    public function compose($view)
    {

        $path 			= \Request::path();		// e.g. vouchers/1
        $editUrl 		= '/'. $path . '/edit';	// e.g. vouchers/1/edit
        $urlPath 		= explode('/', $path);	// e.g. vouchers
        $editAccess 	= $urlPath[0] . '.edit';// e.g. vouchers.edit

        $data =[
            'editUrl'     =>  $editUrl,
            'editAccess'  =>  $editAccess ,
        ];

        $view->with($data);

    }
}