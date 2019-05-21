<?php

namespace App\Admin\Modules\Blacklist\Requests;

use App\Admin\Helpers\Rules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreRequest
 *
 * Request for storing a Blacklist model
 *
 * @package App\Admin\Modules\Blacklist\Requests
 */
class StoreRequest extends FormRequest
{
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
	 * Get the validation rules that apply to the request
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = [
			'mac' 		=> Rules::MAC . '|unique:whitelist,mac,null,id,site,' . session('admin.site.loggedin'). '|unique:blocked,mac,null,id,site,' . session('admin.site.loggedin'),
			'reason' 	=> 'required|min:3|max:128'
		];

		// AJAX request could originate from blocking a Guest from the manage guests edit page
		if(!$this->ajax())
			$rules['site_or_estate'] = 'required|in:site,estate';

        // If unblocking, don't check for duplicates in the database
        if($this->input('new-status') === 'unblocked')
            $rules['mac'] = Rules::MAC;

		return $rules;
	}

	/**
	 * Get the error messages for the defined validation rules.
	 *
	 * @return array
	 */
	public function messages()
	{
		return [
			'mac.unique' => trans('validation.unique-mac-black-or-white')
		];
	}
}