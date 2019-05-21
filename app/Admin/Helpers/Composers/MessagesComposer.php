<?php

namespace App\Admin\Helpers\Composers;

use \App\Admin\Helpers\Messages;

class MessagesComposer {

    /**
     * Setting up the jsMessage variable to be sent to the view
     * @param $view
     */
    public function compose($view)
    {
        $jsMessage = $this->getMessage();

        $view->with('jsMessage', $jsMessage);

    }

    /**
     * Gets the last message from the admin.messages Session or the last Error that happens
     * @return mixed
     */
    public function getMessage()
    {
        $jsMessage = [];

        if(session()->has('messages'))
        {
            foreach (session('messages') as $key => $message)
            {
                $jsMessage = $message;

                if($message[0] == Messages::ERROR_MSG)
                {
                    break;
                }
            }
        }
        return $jsMessage;
    }
}