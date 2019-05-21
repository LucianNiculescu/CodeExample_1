<?php
namespace App\Admin\Modules\Forms;

use \App\Admin\Helpers\BasicCRUD;
use App\Models\AirConnect\Portal as PortalModel;
use App\Models\AirConnect\Question as QuestionModel;
use App\Models\AirConnect\Form as FormModel;
use App\Models\AirConnect\QuestionAttribute as QuestionAttributeModel;
use App\Admin\Helpers\Rules;
use App\Models\AirConnect\Site;
use App\Models\AirConnect\FormExcludedPackages as FormExcludedPackagesModel;
use App\Models\AirConnect\Translation;

class CRUD extends BasicCRUD
{
    public $rules =
        [
            'name'		    =>	Rules::REQUIRED . '|max:255',
        ];

    public $languages = [];

	// Array of question types
	public $questionTypes = [
		'checkbox' 	=> "{{trans('admin.checkbox')}}",
		'colour' 	=> "{{trans('admin.colour')}}",
		'date' 		=> "{{trans('admin.date')}}",
		'email' 	=> "{{trans('admin.email')}}",
		'html' 		=> "HTML",
		'number' 	=> "{{trans('admin.number')}}",
		'select'	=> "{{trans('admin.select')}}",
		'text' 		=> "{{trans('admin.text')}}",
		'range' 	=> "{{trans('admin.range')}}"
	];

	public $questionAttributes = [
		'defaultCheckbox'=> 'checkbox',
		'default' 		=> 'text',
		'maxlength' 	=> 'number',
		'minimum' 		=> 'number',
		'maximum' 		=> 'number',
		'option-keys'	=> 'option',
		'sms-validation' => 'checkbox',
        'email-validation' => 'checkbox',
	];

	public $questionAttributesPerLanguage = [
		'label' 		=> 'text',
		'placeholder' 	=> 'text',
		'option'	 	=> 'text',
		'html'		 	=> 'text',
	];


    /**
     * CRUD constructor.
     * Constructing the needed Model
     * Setting the site to be loggedin site
     * And filling the all portal attribute array
     * @param $model
     */
    public function __construct($model)
    {
        parent::__construct($model);
        $this->successMsg = trans('admin.form-saved');
		$this->languages = Translation::getLanguages();

    }

    /**
     * Creating a new portal and its attributes
     */
    public function create()
    {
		// Creating active Form
        $this->requestData['status'] = 'active';
        $this->requestData['site_id'] = session('admin.site.loggedin');
        parent::create();

		// Creating questions and attach them to the form
		if(isset($this->requestData['questionTypes']))
		{
			$createdQuestions = $this->createQuestions();
			$this->modelObject->questions()->attach($createdQuestions );
		}

/*		if(isset($this->requestData['knownQuestions']))
			$this->modelObject->questions()->attach($this->requestData['knownQuestions'] );*/

		// Attach created form to portal(s)
		if(isset($this->requestData['portals']))
			$this->modelObject->portals()->attach($this->requestData['portals'] );

		// Create Excluded packages rows
		if(isset($this->requestData['excludedPackages']))
		{
			$this->insertExcludedPackages($this->requestData['excludedPackages']);
		}
    }

	/**
	 * Inserting excluded packages the DB
	 * @param $excludedPackages
	 */
    private function insertExcludedPackages($excludedPackages)
	{
		$packagesToExclude = [];
		foreach($excludedPackages as $excludedPackage)
		{
			$packagesToExclude[] = ['form_id' => $this->modelObject['id'], 'package_type' => $excludedPackage];
		}

		FormExcludedPackagesModel::insert($packagesToExclude);
	}

	/**
	/**
     * Updating existing Form and its attributes
     * @param $id
     */
    public function update($id)
    {
        // Updating Form
        parent::update($id);

		if(!\Request::ajax())
		{
			// Getting list of questions attached to the form
			$questions = $this->modelObject->questions()->get()->keyBy('id')->toArray();

			// Detaching all questions and Attach the questions in the form
			$this->modelObject->questions()->detach();

			// Delete questions and inside the model it will cascade delete the attributes
			if(!empty($questions))
				$this->deleteQuestions(array_keys($questions));

			// Create questions and attach them to the form
			if(isset($this->requestData['questionTypes']))
			{
				$createdQuestions = $this->createQuestions();
				$this->modelObject->questions()->attach($createdQuestions );
			}

			// Detaching all portals and Attach the portals in the form
			$this->modelObject->portals()->detach();

			//dd($this->requestData['portals']);
			// Attach form to portal(s)
			if(isset($this->requestData['portals']))
				$this->modelObject->portals()->attach($this->requestData['portals'] );

			// Delete existing Excluded packages rows
			FormExcludedPackagesModel::where('form_id', $this->modelObject['id'])->delete();

			// Insert new Excluded packages rows
			if(isset($this->requestData['excludedPackages']))
			{
				$this->insertExcludedPackages($this->requestData['excludedPackages']);
			}

		}
    }


	/**
	 * Reading Forms table to get all forms attached to any of the portals in this site
	 * @return mixed
	 */
	public static function getAllForms()
	{
		$site = session('admin.site.loggedin');

		$forms = FormModel::where('site_id', $site)
			->where('status', '!=', 'deleted')
			->get()
			->keyBy('id');

		return $forms;
	}

	/**
	 * Reading Forms table to get all forms attached to any of this site
	 * @return mixed
	 */
    public static function getAllFormsWithRelations()
    {
        $site = session('admin.site.loggedin');

		$forms = FormModel::with(['questions', 'portals.site'])
			->where('site_id', $site)
			->where('status', '!=', 'deleted')
			->get()
			->keyBy('id');

        return $forms;
    }


	/**
	 * Getting all Portals attached to the current Form $id
	 * @return mixed
	 */
    public static function getPortals($id)
    {
		$portals = PortalModel::select('id')
			->whereHas('forms', function($q) use ($id){
				$q->where('forms.id', $id);})
			->where('status', '!=', 'deleted')
			->get()
			->keyBy('id')
			->toArray();

        return $portals;
    }


	/**
	 * Getting all Questions attached to the current Form $id
	 * @return mixed
	 */
    public static function getQuestions($id)
    {
    	$questions = QuestionModel::select('id')
			->whereHas('forms', function($q) use ($id){
				$q->where('forms.id', $id);})
			->where('status', '!=', 'deleted')
			->get()
			->keyBy('id')
			->toArray();

        return $questions;
    }


	/**
	 * Get the Form to fill the Form in Edit mode
	 * @return mixed
	 */
    public static function getFormWithQuestions($id)
    {
    	$form = FormModel::findOrFail($id); /*with('portals.attributes')->  ???????*/

        return $form;
    }

	/**
	 * Gets the list of predefined questions where portal = 0
	 * @return array
	 */
	public static function getPredefinedQuestions()
	{
		$predefinedQuestions = QuestionModel::orderBy('name')
			->where('status', '!=', 'deleted')
			->where('portal', '0')
			->get()
			->keyBy('id')
			->toArray();

		$result = [];

		foreach ($predefinedQuestions as $key => $question)
		{
			$result[$key] = $question['name'];
		}

		return $result;
	}


	/**
	 * Reading the Questions table and list questions that were created before for any portal for this site
	 * @return mixed
	 */
    public static function getKnownQuestions($id = null)
    {
        $site = session('admin.site.loggedin');

		$knownQuestions = QuestionModel::orderBy('name')
			->whereHas('forms', function($q) use ($site, $id)
			{
				$q->where('forms.site_id', $site)
				->where('forms.status', 'active')
				->where('forms.id', '!=', $id);
			})
			->where('status', 'active')
			->get()
			->groupBy('name')
			->toArray();

		$result = [];

		// Since it is a group by now the key is the name and the value is an array of questions
		// We need only the first question in the group
		foreach ($knownQuestions as $name => $question)
		{
			$result[$question[0]['id']] = $name;
		}

        return $result;
    }


	/**
	 * Reading the Questions table and list questions that were created for the current form
	 * @return mixed
	 */
    public static function getFormQuestions($id)
    {
		$formQuestions = QuestionModel::orderBy('name')
			->whereHas('forms', function($q) use ($id)
			{
				$q->where('forms.id', $id);
			})
//			->where('status', '!=', 'deleted')
			->with(['attributes' => function($q){$q->orderBy('question_attribute.id');}])
			->get()
			->keyBy('id')
			->toArray();

        return $formQuestions;
    }

	/**
	 * Reading the Questions table and list questions that were created for the current form
	 * @return mixed
	 */
    public static function getQuestion($id)
    {
		$question = QuestionModel::where('id', $id)
			->with(['attributes' => function($q){$q->orderBy('question_attribute.id');}])
			->get()
			->keyBy('id')
			->toArray();

        return $question;
    }


	/**
	 * Create questions and question attributes
	 * @return array
	 */
	private function createQuestions()
	{
		//['portal', 'name', 'type', 'pattern', 'required', 'order', 'status'];
		$questionFields = [];
		$createdQuestions=[];
		$order = 0;

		// Looping inside the question types
		foreach($this->requestData['questionTypes'] as $questionCount=>$type)
		{
			// Check if question name is filled or not, then create the question
			if($this->requestData['question-name'][$questionCount] != '')
			{
				if($this->requestData['readonlyType'][$questionCount] == 1)
					$questionFields['status'] =  'readonly';
				else
					$questionFields['status'] =  'active';

				$questionFields['order'] 	=  $order;
				$questionFields['portal'] 	=  null; //(isset($this->requestData['portals'][0])) ? $this->requestData['portals'][0] : null;
				$questionFields['type'] 	=  $type ;
				$questionFields['name']	 	=  $this->requestData['question-name'][$questionCount];
				$questionFields['required'] =  $this->requestData['question-required'][$questionCount];

				// Creating the question
				$question = new QuestionModel();
				$newQuestion = $question::create($questionFields);

				// Creating associated attributes that are language specific
				$this->createQuestionAttributesPerLanguage($questionCount, $newQuestion->id);

				// Creating attributes that are not language specific
				$this->createQuestionAttributes($questionCount, $newQuestion->id);

				// List of created questions
				$createdQuestions[] = $newQuestion->id;
				$questionFields = [];
			}

			$order++;
		}

		return $createdQuestions;
	}

	/**
	 * Creating language specific attributes where type equals the language code
	 * @param $questionCount
	 * @param $questionId
	 */
	private function createQuestionAttributesPerLanguage($questionCount, $questionId)
	{
		$questionAttributes = [];
		foreach($this->languages as $langKey => $languageValue)
		{
			foreach ($this->questionAttributesPerLanguage as $attr => $attrType)
			{
				$value = isset($this->requestData['question-'.$attr][$questionCount][$langKey])? $this->requestData['question-'.$attr][$questionCount][$langKey] : '';
				if( $value != '')
				{
					if(is_array($value))
					{
						$name = $this->requestData['question-'.$attr.'-keys'][$questionCount];
						$count = 0;
						foreach($value as $v)
						{
							if($v != '')
							{
								$questionAttributes[] = [
									'ids' 	=> $questionId,
									'name' 	=> $name[$count],
									'type' 	=> $langKey,
									'value' => $v,
									'status'=> 'active'];
								$count++;
							}
						}
					}
					else
					{
						$questionAttributes[] = [
							'ids' 	=> $questionId,
							'name' 	=> $attr,
							'type' 	=> $langKey,
							'value' => $value,
							'status'=> 'active'];
					}
				}
			}
		}
		$questionAttribute = new QuestionAttributeModel();
		$questionAttribute->insert($questionAttributes);
	}

	/**
	 * Creating question attributes
	 * @param $questionCount
	 * @param $questionId
	 */
	private function createQuestionAttributes($questionCount, $questionId)
	{
		if($this->requestData['readonlyType'][$questionCount] == 1)
			$status =  'readonly';
		else
			$status =  'active';

		$questionAttributes = [];
		foreach ($this->questionAttributes as $attr => $attrType)
		{
			$value = isset($this->requestData['question-'.$attr][$questionCount])? $this->requestData['question-'.$attr][$questionCount] : '';

			if($attr == 'defaultCheckbox' and $value == '0')
			{/* ignore */}
			elseif($value != '')
			{
				if(is_array($value))
				{
					foreach($value as $v)
					{
						if($v != '')
						{
							$questionAttributes[] = [
								'ids' 	=> $questionId,
								'name' 	=> $v,
								'type' 	=> $attrType,
								'value' => $v,
								'status'=> $status];
						}
					}
				}
				else
				{
					$questionAttributes[] = [
						'ids' 	=> $questionId,
						'name' 	=> $attr,
						'type' 	=> $attrType,
						'value' => $value,
						'status'=> $status];
				}
			}
		}

		$questionAttribute = new QuestionAttributeModel();
		$questionAttribute->insert($questionAttributes);
	}

	/**
	 * Deleting questions when updating the form, and recreated them again.
	 * @param $questions
	 */
	private function deleteQuestions($questions)
	{
		$question = new QuestionModel();
		$question::whereIn('id', $questions)->delete();

		$questionAttribute = new QuestionAttributeModel();
		$questionAttribute::whereIn('ids', $questions)->delete();
	}

}