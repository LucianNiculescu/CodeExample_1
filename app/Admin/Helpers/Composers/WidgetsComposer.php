<?php

namespace App\Admin\Helpers\Composers;

use App\Admin\Widgets\Logic as Widgets;
use App\Models\AirConnect\AdminWidget as AdminWidgetModel;
use App\Models\AirConnect\RoleWidget as RoleWidgetModel;


class WidgetsComposer {

	/**
	 * Gets the correct list of widgets for the user
	 * @param $view
	 */
	public function compose($view)
	{
		// Checks the AdminWidget table if there is any widgets stored there
		// i.e. user had saved the dashboard already by moving widgets around ...etc
		$widgets = Widgets::getWidgetData(AdminWidgetModel::class, ['admin_id' => session('admin.user.id')]);

		// If AdminWidgets is empty then will check the RoleWidget table
		// i.e. the Default settings of the widgets for the role
		if( $widgets->isEmpty())
			$widgets = Widgets::getWidgetData(RoleWidgetModel::class, ['role_id' => session('admin.user.role_id')]);

		// Removing any null values
		$widgets = array_filter($widgets->toArray(), function($value) { return $value !== null; });

		// Will merge the widgets retrieved from adminWidgets or roleWidgets to the widgets comming from the widgets table
		$widgets = $widgets + Widgets::getAllowedWidgets()->toArray();

		// Run the view and pass widgets as a variable
		$view->withWidgets( $widgets);
	}
}