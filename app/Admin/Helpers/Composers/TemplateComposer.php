<?php

namespace App\Admin\Helpers\Composers;

use App\Models\AirConnect\AdminTemplate as AdminTemplate;



class TemplateComposer {
    
    private $templateName = 'system';
    private $templates = null;
    
    public function compose($view)
    {
        $view->with('template',  $this->getTemplate());
    }

    /**
     * Get Template
     * runs the setTemplate function and returns the template name to be composed into the view
     * @return string
     *
     */
    public function getTemplate()
    {
        $this->setTemplate();
        return $this->templateName;
    }

    /**
     * Set Template
     * Checks or template in session, user profile, current URL or referrer URL
     * @return string
     */
    private function setTemplate()
    {
		if( !empty(\Request::all()['template']) ) //check if there is a template in the GET URL
			$this->setTemplateName('name', \Request::all()['template']);
		else {
			if( session()->has('admin.user.template') )  // checks the session for a user template, if in session pass to function to set the template
				$this->setTemplateFromSession();
			elseif( session()->has('admin.user.id') ) // if the user user id is in session pass to function to set the template name based on user template_id
				$this->checkUserProfileIfLoggedIn();
			else {
				$this->getTemplateForCurrentUrl(); // pass to function to check against current URL
				$this->getTemplateForReferrerUrl(); // pass to function to check against referrer URL
			}
		}
    }

    /**
     * Set Template from Session
     * set the template name as the one in the session
     */
    private function setTemplateFromSession()
    {
        $this->templateName = session( 'admin.user.template' );
    }

    /**
     * Check user profile if logged in
     * if session contains template_id for the user, use setTemplateName
     * function to set the template name based on the session template_id
     */
    private function checkUserProfileIfLoggedIn()
    {
        if( session()->has('admin.user.template_id') )
            $this->setTemplateName('id', session('admin.user.template_id'));
    }

    // TODO: need to change DB table 'referrer' to 'url'

    /**
     * Get template from DB for current URL
     * Gets current URL, use setTemplateName function to set the template name if any have a URL that matches the current URL
     */
    private function getTemplateForCurrentUrl()
    {
        $currentUrl = parse_url( \URL::current(), PHP_URL_HOST);
        $this->setTemplateName('url', $currentUrl);
    }

    /**
     * Get template from DB for previous URL
     * Gets previuos URL, use setTemplateName function to set the template name if any have a URL that matches the previuos URL
     */
    private function getTemplateForReferrerUrl()
    {
        $referrerUrl = parse_url( \URL::previous(), PHP_URL_HOST);
        $this->setTemplateName('url', $referrerUrl);
    }

    /**
     * Set Session with the template name
     */
    private function setSessionTemplate()
    {
        \Session::put( 'admin.user.template', $this->templateName );
    }

    /**
     * Get all templates
     * if there hasnt already been a template name set, get all admin templates from the database
     */
    private function getAllTemplates()
    {
        if($this->templates == null){
            $this->templates = AdminTemplate::all();
        }
    }

    /**
     * Set template name
     * gets all templates, searches for templates base on parameters passed in and returns if not empty
     * also adds the template name to the session if not empty
     * @param $key
     * @param $value
     */
    private function setTemplateName($key, $value)
    {
        $this->getAllTemplates();
        $template = $this->templates->where($key, $value)->first();

        if(!empty($template)) {
            $this->templateName = $template->name;
            $this->setSessionTemplate();
        }
    }

}