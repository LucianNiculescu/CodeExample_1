<?php

namespace App\Admin\Modules\Whitelist\Requests;

use App\Admin\Helpers\Rules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreRequest
 *
 * Request for storing a Whitelist model
 *
 * @package App\Admin\Modules\Whitelist\Requests
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
		return [
			'mac' 			=> Rules::MAC . '|unique:blocked,mac,null,id,site,' . session('admin.site.loggedin') . '|unique:whitelist,mac,null,id,site,' . session('admin.site.loggedin') ,
			'description' 	=> 'max:500',
			'site_or_estate' => 'required|in:site,estate'
		];
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