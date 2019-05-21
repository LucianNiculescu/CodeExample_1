<?php

namespace App\Admin\Modules\Blacklist;

use App\Admin\Helpers\BasicDatatable;
use App\Models\AirConnect\Blocked;
use App\Transformers\BlockedTransformer;

class SetupViewData
{
	/**
	 * Returns the data to pass to the view to build the blacklist index datatable
	 *
	 * @return array
	 */
    public static function clientSideDatatable()
    {
		// Grab blacklist for current site and all children
		$siteIds 	= session('admin.site.children');
		$blacklist 	= Blocked::with('admin', 'parentSite')->whereIn('site', $siteIds)->get();
		$rows		= BlockedTransformer::transform($blacklist)
											->as('object')
											->into('datatables');
		$columns 	= ['id' => '', 'site' => '', 'mac' => '', 'reason' => '', 'blocker' => '', 'created' => ''];

    	return [
    		'title' 		=> trans('admin.blacklist-title'),
			'description' 	=> trans('admin.blacklist-description'),
			'columns'		=> $columns,
			'rows' 			=> $rows,
			'tableId' 		=> 'manage-blacklist',
			'route' 		=> 'manage/blacklist',
			'showActions' 	=> BasicDatatable::showActions('manage/blacklist'),
			'customActions'	=> ['delete'],
			'hideCreate'	=> true
		];
    }
}