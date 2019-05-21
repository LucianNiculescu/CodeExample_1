<?php

namespace App\Admin\Modules\Blacklist\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\AirConnect\Blocked;

/**
 * Class DeleteRequest
 *
 * Request for storing a Blacklist model
 *
 * @package App\Admin\Modules\Blacklist\Requests
 */
class DeleteRequest extends FormRequest
{
	/**
	 * Authorize the request by verifying the blocked record to be deleted belongs to the
	 * currently logged in Site or one of it's children
	 *
	 * @TODO   Test User authorization
	 * @return bool
	 */
	public function authorize()
	{
		$blockedId = $this->route('blacklist');
		return Blocked::where('id', $blockedId)
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