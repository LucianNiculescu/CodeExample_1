<?php
/**
 * Created by PhpStorm.
 * User: sheri
 * Date: 20-Sep-17
 * Time: 3:03 PM
 */

namespace App\Admin\Search;

use Illuminate\Routing\Controller as BaseController;
use App\Helpers\DateTime;

class Controller extends BaseController
{

	/**
	 * Searching Elastic
	 */
	public static function postSearch()
	{
		$formData = \Request::all();
		// Getting the search from the search form or from session
		$search		= $formData['search'] ?? session('admin.search') ?? '';
		$searchType	= $formData['search_type'] ?? session('admin.search_type') ?? 'all';
		// Adding search to session
		session(['admin.search' => $search]);
		session(['admin.search_type' => $searchType]);

		$from	= $formData['from'] ?? 0;
		$repository = \App::make('App\Admin\Search\SearchRepository');
		$result = $repository->search($search, $from);

		return view('admin.search.advanced-search', [
			'searchTypeList' 	=> self::getSearchTypeList(),
			'result'		=> $result['hits']['hits'] ?? [],
			'title'			=> trans('admin.search') . ': ' . $search,
			'description'	=> trans('admin.search-found-in',[
				'total' => number_format($result['hits']['total'] ?? 0),
				'time' 	=> DateTime::milliSeconds2readable($result['took'])]),
			'total'			=> $result['hits']['total'],
			'from'			=> $from,
			'hideCreate'	=> true,
			'search'		=> $search,
			'searchType'	=> $searchType,
			'editGuests'	=> \Gate::allows('access', 'manage.guests.edit'),
			'editGateways'	=> \Gate::allows('access', 'networking.gateways.edit') or \Gate::allows('access', 'all-gateways.edit'),
			'editHardware'	=> \Gate::allows('access', 'networking.hardware.edit'),
		]);
	}


	/**
	 * Will open the search page
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getSearch()
	{
		return view('admin.search.advanced-search',[
			'title'				=> trans('admin.search-title'),
			'description'		=> trans('admin.search-desc'),
			'searchTypeList' 	=> self::getSearchTypeList(),
		]);
	}

	/**
	 * Getting search type list depending on the permission
	 * @return array
	 */
	public static function getSearchTypeList()
	{
		$searchTypeList = ['all'		=> trans('admin.all')];

		if(\Gate::allows('access', 'manage.guests'))
			$searchTypeList['user'] = trans('admin.guests');

		if(\Gate::allows('access', 'networking.gateways') or \Gate::allows('access', 'all-gateways'))
			$searchTypeList['gateway'] = trans('admin.gateways');

		if(\Gate::allows('access', 'networking.hardware'))
			$searchTypeList['hardware'] = trans('admin.hardware');

		return $searchTypeList;
	}
}