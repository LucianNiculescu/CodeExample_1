<?php


namespace App\Admin\Modules\Languages;
use Illuminate\Routing\Controller as BaseController;
use App\Models\AirConnect\Language as LanguageModel;
use App\Admin\Helpers\Messages;
use App\Helpers\Language;

class Controller extends BaseController
{
	/**
	 * Showing the table of the languages
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{

	//	dd(Language::getAdminLanguages());
		return view('admin.modules.languages.index' , [
			'title'			=> trans('admin.languages-title'),
			'description'	=> trans('admin.languages-desc'),
			'languages' 	=> LanguageModel::get()->toArray()
		]);
	}

	/**
	 * Saving the languages
	 * @return mixed
	 */
	public function store()
	{
		$data = \Request::all();

		foreach ( array_keys($data) as $record)
		{
			$parts = explode('-', $record);

			// If it has 2 parts then it is a switch where it's name is like admin-de or portal-tr
			if(sizeof($parts) == 2)
			{
				// Retrieving the record by the key and update the correct column.
				LanguageModel::where('key', $parts[1])
					->update([$parts[0] => $data[$record]]);
			}
		}

		Messages::create(Messages::SUCCESS_MSG, 'Languages Saved');
		return \Redirect::to('/languages');
	}
}