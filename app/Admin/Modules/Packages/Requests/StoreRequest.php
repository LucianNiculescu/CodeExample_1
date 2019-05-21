<?php

namespace App\Admin\Modules\Packages\Requests;

use App\Admin\Helpers\Messages;
use App\Admin\Modules\Pms\Logic as Pms;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\AirConnect\Package;
use App\Models\AirConnect\Site;

class StoreRequest extends FormRequest
{
	/**
	 * The validation rules for a Package
	 *
	 * @var array
	 */
	public $rules = [
		'name'			=> 'required|min:3|max:32',
		'type'			=> 'required',
		'duration'		=> 'numeric',
		'cost'			=> 'numeric',
		'description'	=> 'max:255',
		'download'		=> 'numeric',
		'upload'		=> 'numeric',
	];

	/**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
    	// Default, authorisation dealt with elsewhere
        return true;
    }

	/**
	 * Add validation rule after other validation to only allow one free
	 * email type package to be created per site
	 *
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function getValidatorInstance()
	{
		// Only apply rule on create
		if($this->method() !== 'POST')
			return parent::getValidatorInstance();

		// After validation, implement logic
		return parent::getValidatorInstance()->after(function ($validator) {
			// If the user is trying to create a free email package, and the site already has one
			if(($this->type == 'email' && $this->cost == 0)
				&& Site::loggedIn()->freePackages()->count())
			{
				Messages::create('error', trans('admin.packages-one-free-per-site') );

				// Validation fails - This does go to screen
				$validator->errors()->add('force-validation', trans('admin.packages-one-free-per-site'));
			}
		});
	}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
    	// AJAX Requests are for just deactivating and deleting, only require status from request
		if($this->ajax())
			return [
				'status' => 'required|in:' . implode(',', Package::$statuses)
			];

		// When creating, we should validate the type field's contents
		if($this->method() === 'POST')
		{
			// Check if PMS is enabled or not
			Pms::pmsCheck();

			// Add in validation for the packages types
			$this->rules['type'] .=  '|in:' . implode(',', Package::$types);
		} else { // Updating

			// Need whether we're updating active transactions which are active against the
			// package (or replacing the current package)
			$this->rules['update_transactions'] = 'required|in:yes,no,replace';

			// We need a gateway if we're updating active transaction
			$this->rules['gateway_id'] = 'required_if:update_transactions,yes|exists:gateway,id';
		}

		// Return the rules to validate the request
		return $this->rules;
    }
}
