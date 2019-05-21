<?php

namespace App\Admin\Modules\Whitelist\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\AirConnect\Whitelist;

/**
 * Class DeleteRequest
 *
 * Request for deleting a Whitelisd model
 *
 * @package App\Admin\Modules\Whitelist\Requests
 */
class DeleteRequest extends FormRequest
{
	/**
	 * Authorize the request by verifying the whitelsit record to be deleted belongs to the
	 * currently logged in Site or one of it's children
	 *
	 * @TODO   Test User authorization
	 * @return bool
	 */
	public function authorize()
	{
		$whitelistId = $this->route('whitelist');
		return Whitelist::where('id', $whitelistId)
						->whereIn('site', session('admin.site.children'))
						->exists();
	}

	/**
	 * Get the validation rules that apply to the request
	 *
	 * @return array
	 */
	public function rules()
	{
		return [];
	}
}