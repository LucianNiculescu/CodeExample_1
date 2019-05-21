<?php

namespace App\Admin\Modules\Whitelist;

use App\Admin\Helpers\BasicDatatable;
use App\Models\AirConnect\Whitelist;
use App\Transformers\WhitelistTransformer;

class SetupViewData
{
	/**
	 * Returns the data to pass to the view to build the whitelist index datatable
	 *
	 * @return array
	 */
    public static function clientSideDatatable()
    {
		// Grab whitelist for current site and all children
		$siteIds 	= session('admin.site.children');
		$whitelist 	= Whitelist::with('admin', 'parentSite')->whereIn('site', $siteIds)->get();
		$rows		= WhitelistTransformer::transform($whitelist)
											->as('object')
											->into('datatables');
		$columns 	= ['id' => '', 'site' => '', 'mac' => '', 'description' => '', 'performedby' => '', 'created' => ''];

    	return [
    		'title' 		=> trans('admin.whitelist-title'),
			'description' 	=> trans('admin.whitelist-description'),
			'columns'		=> $columns,
			'rows' 			=> $rows,
			'tableId' 		=> 'networking-whitelist',
			'route' 		=> 'networking/whitelist',
			'showActions' 	=> BasicDatatable::showActions('networking/whitelist'),
			'customActions'	=> ['delete'],
			'hideCreate'	=> true
		];
    }
}