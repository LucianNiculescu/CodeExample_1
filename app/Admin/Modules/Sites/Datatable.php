<?php
namespace App\Admin\Modules\Sites;

use \App\Admin\Helpers\BasicDatatable;

class Datatable extends BasicDatatable
{

	/**
	 * Setting up datatable and send it back
	 * @return mixed
	 */
	public static function getTable($estatePage = false)
	{
		$route = 'sites';

		$table = parent::getBasicTable($route, $estatePage);

		$table = $table	->addColumn(
							'version',
							trans('admin.site-id'),
							trans('admin.name'),
							trans('admin.reference'),
							trans('admin.type') ,
							trans('admin.actions') )
						->setOptions( [
							'retrieve'	=> true,
							'order' => [1, "asc"],
							'aoColumns' => [
								['visible' => false],
								null,
								null,
								null,
								null,
								// Actions column will only be visible if you have the right permissions
								['sWidth' => '70px'	, 'visible' =>  self::showActions($route), 'aTargets' => [ 4 ], 'aDataSort' => [ 0 ]]
							]]);
		
			return $table->noScript();
	}
	/**
	 * Making the Datatable
	 * showColumns is a list of the titles of the columns
	 * addColumn is adding a column one by one, the return of the call back function determines how the output in this column will look like
	 * searchColumns list of searchable columns
	 * orderColumns list of ordable columns
	 * @param $query
	 * @return mixed
	 */
	public static function makeTable($query, $estatePage)
	{
		$route = '/sites';

		if($estatePage)
			$route = '/manage/sites';

		$link 		= '<a href="/dashboard/%s">%s</a>';
		$cellValue 	= '<span class="disabled">%s</span>';

		return self::query( $query )
			->showColumns( 'version', 'id', 'name', 'reference' , 'type', 'actions')
			->addColumn( 'version', 	function( $site ) use ($link, $cellValue) { return $site->version; } )
			->addColumn( 'id', 			function( $site ) use ($link, $cellValue) { if($site->version == 3 ) return sprintf($link, $site->id, $site->id); else return sprintf($cellValue, $site->id); } )
			->addColumn( 'name', 		function( $site ) use ($link, $cellValue) { if($site->version == 3 ) return sprintf($link, $site->id, $site->name); else return sprintf($cellValue, $site->name); } )
			->addColumn( 'reference', 	function( $site ) use ($link, $cellValue) { if($site->version == 3 ) return sprintf($link, $site->id, $site->reference); else return sprintf($cellValue, $site->reference); } )
			// Shows the 'site_attribute.value' with first character in capital
			->addColumn( 'type', 		function( $site ) use ($link, $cellValue) {
				$type = ($site->type == ''? trans('admin.n-a'):trans('admin.'.$site->type));
				if($site->version == 3 ) return sprintf($link, $site->id, $type); else return sprintf($cellValue, $type); } )

			->addColumn( 'actions',		function( $site ) use ($route, $estatePage)
			{
				$actions = '';

				// if user has no permission to do any of these actions then return nothing
				if (!self::showActions($route))
				{
					return $actions;
				}

				$params = 'href="'.$route . '/' .$site->id.'" data-id="'.$site->id.'" data-name="'.$site->name.'" data-route="'.$route.'"';

				if($site->version == 3 )
				{
					$actions = self::writeActions($site, $route, $params);

				}
				else
				{
					//Checking sites.upgrade permission to show the delete link or not
					if (self::canAccess($route, 'upgrade'))
					{
						$actions .= '<a title="' . trans("admin.upgrade-v3") . ' \'' .$site->name . '\'" class="action action_upgrade" data-version="3" '.$params.' ><i class="fa fa-rocket action text-info"></i></a>';
					}
					else
					{
						$actions .= '<span title="' . trans("admin.upgrade-needed") . '"><a class="action disabled"  href="javascript:void(0)" ><i class="fa fa-rocket action text-info"></i></a></span>';
					}
				}

				return $actions;

			})
			->searchColumns	('site.name','site.id', 'site.reference', 'site.version', 'site_attribute.value')
			->orderColumns 	('version','name','id', 'reference', 'type')
			->make();
	}


	public static function writeActions($site, $route, $params)
	{
		$actions = '';
		// Configuring the color of the icon
		$statusColor = ($site->status == 'active') ? 'success' : 'danger';

		// Configuring the shape of the toogle icon
		$statusIcon  = ($site->status == 'active') ? 'on' : 'off';

		// Configuring the title of the icon
		$statusTitle = ($site->status == 'active' ? trans('admin.de-activate') : trans('admin.activate') ). ' \'' . $site->name . '\'';

		// Checking sites.activation permission to show the activation link or not
		if (self::canAccess($route, 'activate'))
		{
			$actions .= '<a title="' . $statusTitle . '" class="action action_status" data-status="'.$site->status.'" '.$params.' ><i class="fa fa-toggle-'. $statusIcon .' action text-' . $statusColor . '"></i></a>';
		}

		//checking sites.edit permission to show the edit link or not
		if (self::canAccess($route, 'edit'))
		{
			$actions .= '<a title="' . trans("admin.edit") . ' \'' . $site->name. '\'" 	class="action action_edit" href="' .$route . '/'  .$site->id . '/edit"><i class="fa fa-pencil action text-info"></i></a>';
		}

		//Checking sites.delete permission to show the delete link or not
		if (self::canAccess($route, 'delete'))
		{
			$actions .= '<a title="' . trans("admin.delete") . ' \'' .$site->name . '\'" class="action action_delete" '.$params.' ><i class="fa fa-trash-o action text-danger"></i></a>';
		}
		return $actions;
	}
}