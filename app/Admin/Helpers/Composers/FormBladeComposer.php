<?php

namespace App\Admin\Helpers\Composers;


/**
 * I don't think this is used anywhere
 * Class FormBladeComposer
 * @package App\Admin\Helpers\Composers
 */
class FormBladeComposer {

    /**
     * Dummy for now may be needed in the future
     * Setting up the $data variable to be sent to the view
     * @param $view
     */
    public function compose($view)
    {
/*        $path 			= \Request::path();
        $createUrl 		= '/' . $path .'/create';
        $createAccess 	= '/' . $path .'.create';
*/

        $data =[
/*            'createUrl'     =>  $createUrl,
            'createAccess'  =>  $createAccess ,
*/
        ];

        $view->with($data);

    }
}