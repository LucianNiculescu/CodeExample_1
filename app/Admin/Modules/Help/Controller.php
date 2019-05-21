<?php
namespace App\Admin\Modules\Help;

use Illuminate\Routing\Controller as BaseController;
use App\Admin\Modules\Help\Logic as Help;

/**
 * Class Controller for Help
 */
class Controller extends BaseController
{
	/**
	 * Display the specified Help
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function show($id)
	{
		$data = [
			'title' 		=> trans('admin.help') .': ' .$id,
			'description' 	=> trans('admin.help-description'),
		];
		return view('admin.modules.help.show' , $data);
	}

	/**
	 * Display a listing of the Help
	 */
	public function index()
	{
		// Set the help array
		Help::setHelpArray();

		$data = [
			'title' 		=> trans('admin.help'),
			'description'	=> trans('admin.help-description'),
			'hideCreate' 	=> true,
			'helpArray' 	=> Help::getHelpArray()
		];

		return view('admin.modules.help.index', $data);
	}
}