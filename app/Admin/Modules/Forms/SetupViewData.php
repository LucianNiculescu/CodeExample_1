<?php

namespace App\Admin\Modules\Forms;

use App\Admin\Modules\Portals\CRUD as Portals;
use App\Admin\Helpers\BasicDatatable;
use App\Portal\Questions\Validation\Sms\SmsService;
use App\Admin\Modules\Forms\Logic as Forms;

class SetupViewData
{

    public static $nonBoolAttributes =
        [
            'language'          => '' ,
        ];

    public static $portalAttributes = [] ;


	/**
	 * Getting all forms information to fill the datatable
	 * @return array
	 */
    public static function index()
    {
		$rows = CRUD::getAllFormsWithRelations();

		$route = 'manage/forms';
		$tableId = 'manage-forms';

		$showActions = BasicDatatable::showActions($route);

		$columns = [
			'name'		=> '' ,
			//'excludedPackages'	=> '\App\Admin\Helpers\Datatables\RelatedColumn',
			'questions'	=> '\App\Admin\Helpers\Datatables\RelatedColumn',
			'portals'	=> '\App\Admin\Helpers\Datatables\RelatedColumn',
			'updated'	=> '\App\Admin\Helpers\Datatables\DateColumn',
		];

		$data =  [
			'title' 				=> trans('admin.manage-forms-title'),
			'description'			=> trans('admin.manage-forms-desc'),
			'columns'				=> $columns,
			'rows' 					=> $rows,
			'tableId'				=> $tableId,
			'route'				    => $route,
			'showActions'           => $showActions,
		];

		return $data;
	}

	/**
	 * Shows the create form and setup the predefined questions, portals and knownquestions (i.e. used before in other portals)
	 * @return array
	 */
    public static function create()
    {
        // Setup the form's action and method
        $actionUrl = '/' . str_replace ('/create','',\Request::path());
        $hiddenMethod = 'POST';

		$predefinedQuestions = CRUD::getPredefinedQuestions();
		$knownQuestions = CRUD::getKnownQuestions();
		$allPortals = Portals::getPortalsList();

		// Check whether there are SMS providers on the Site
		$providers = (new SmsService())->setSite( logged_in_site() )->getProvidersOnSite()->first();


		if(session('admin.site.type') == 'site')
			$bladeName = 'admin.templates.system.input-fields.basic.select';
		else
			$bladeName = 'admin.templates.system.input-fields.basic.select-with-groups';

        // Data sent to the create page
        $data = [
            'title' 		    => trans('admin.new-form-title') ,
            'description' 	    => trans('admin.new-form-desc'),
            'hiddenMethod' 	    => $hiddenMethod ,
            'actionUrl' 	    => $actionUrl,
			'knownQuestions'	=> $knownQuestions,
			'predefinedQuestions'	=> $predefinedQuestions,
			'allPortals'			=> $allPortals,
			'allPackages'		=> Forms::getAllPackages(),
			'portalsSelectBlade'=> $bladeName,
            'moduleName'        => 'manage.questions',
            'edit'              => false,
			'smsValidationOn'	=> (is_null($providers)) ? 0 : 1
        ];

        return $data;
    }

	/**
	 * Shows the edit form and the JS will get the questions and build the questions part on the fly
	 * @param $id
	 * @return array
	 */
    public static function edit($id)
    {
    	// Need to check if the ID is in the questions user has access to or not
		$forms = CRUD::getAllForms();

		if(!in_array($id, array_keys($forms->toArray())))
			abort('401', trans('error.not-authorized'));

        // Setup the form's action and url
        $actionUrl = '/' . str_replace ('/edit','',\Request::path());
        $hiddenMethod = 'PUT';

		$predefinedQuestions = CRUD::getPredefinedQuestions();
		$knownQuestions = CRUD::getKnownQuestions($id);
		$allPortals = Portals::getPortalsList();
        $form = CRUD::getFormWithQuestions($id);
        $formQuestions = CRUD::getFormQuestions($id);

		// Check whether there are SMS providers on the Site
		$providers = (new SmsService())->setSite( logged_in_site() )->getProvidersOnSite()->first();

		if(session('admin.site.type') == 'site')
			$bladeName = 'admin.templates.system.input-fields.basic.select';
		else
			$bladeName = 'admin.templates.system.input-fields.basic.select-with-groups';

		//dd($form->portals->toArray());
		//dd($form->excludedPackages->toArray());
        // Data to be sent to the Role edit page
        $data =
            [
                'title' 		    =>  trans('admin.edit-form-title') ,
                'description' 	    =>  trans('admin.edit-form-desc'),
                'module'	        => $form,
                'id'                => $id,
                'hiddenMethod' 	    => $hiddenMethod ,
				'knownQuestions'	=> $knownQuestions,
				'predefinedQuestions'	=> $predefinedQuestions,
				'allPortals'		=> $allPortals,
				'formPortals'		=> CRUD::getPortals($id),
				'excludedPackages'	=> Forms::getExcludedPackageTypes($id),
				'portalsSelectBlade'=> $bladeName,
				'allPackages'		=> Forms::getAllPackages(),
                'actionUrl' 	    => $actionUrl,
                'moduleName'        => 'manage.forms',
				'formQuestions'		=> $formQuestions,
                'edit'              => true,
				'smsValidationOn'	=> (is_null($providers)) ? 0 : 1
            ];
        return $data;
    }
}