<?php

namespace App\Admin\Modules\Translations;

use Auth;
use Illuminate\Routing\Controller as BaseController;
use \App\Admin\Helpers\CSV;
use App\Admin\Modules\Translations\Logic as Translations;

/**
 * Class Controller
 * @package App\Admin\Modules\Users
 */
class Controller extends BaseController
{
	/**
	 * Display a form to upload and download the CSV
	 */
	public function index()
	{
		$translationsDatatable = Datatable::getTable();

		$data = [
			'title' 		=> trans('admin.translations'),
			'description'	=> trans('admin.translations-desc'),
			'translationsDatatable'	=> $translationsDatatable,
		];


		return view('admin.modules.translations.index', $data);
	}

	/**
	 * this is called to get the Json object back to the estate route
	 * @return mixed
	 */
	public function getTranslationsDatatable()
	{
		// Create the query to run the estate datatable
		$query = \DB::table( 'translations' );

		// Return the Datatable json
		return Datatable::makeTable($query);
	}

	/**
	 * Downloads from the Translations table into a CSV file
	 */
	public function downloadCSV()
	{
		CSV::downloadFromDB('\App\Models\AirConnect\Translation', 'translations.csv', false);
	}

	public function store()
	{
		return Translations::saveForm();
	}

	public function show()
	{
		abort(404);
	}

	public function destroy()
	{
		abort(404);
	}
	public function create()
	{
		abort(404);
	}
	public function edit()
	{
		abort(404);
	}
	public function update()
	{
		abort(404);
	}
}
