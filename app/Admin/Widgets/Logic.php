<?php

namespace App\Admin\Widgets;

use App\Models\AirConnect\AdminWidget;
use \App\Admin\Helpers\Messages;
use App\Models\AirConnect\Widget as WidgetModel;
use App\Models\AirConnect\Permission as PermissionModel;


class Logic
{
	/**
	 * Get all allowed widgets based on user role
	 * @return mixed
	 */
	public static function getAllowedWidgets()
	{
		$roleId = session('admin.user.role_id');
		$routeName = \Request::route()->getName();
		// If route has a . then get all text after the .
		$routeName = substr($routeName, strpos($routeName, '.') !== false ? strpos($routeName, '.') +1 : 0);

		$siteType = session('admin.site.type');
		// Get an array of permissions that starts with the word 'widgets.'
		$permissions = PermissionModel::where('role_id', $roleId)
			->where('permission', 'like', 'widgets.%')
			->get()
			->pluck('permission')
			->toArray();

		// Removing the word 'widgets.' from the items in the $permissions
		$widgetNames = array_map (function($x){return explode('.', $x)[1];}, $permissions);

		// use the widget array to get the widget data linked to the permission entry
		$allowedWidgets = WidgetModel::whereIn('title', $widgetNames)
			->where('type', $siteType)
			->where('status', '!=', 'inactive')
			->where('routes', 'like', '%' . $routeName .'%')
			->get()
			->keyBy('id')
			->map(function ($item, $key) {
				$item->status = 'inactive';
				return $item;
			});

		// Setting the admin.user.allowed_widgets session variable
		session(['admin.user.allowed_widgets.' . $siteType . '_' . $routeName => $allowedWidgets->keys()]);
		return $allowedWidgets;
	}


	/**
	 * Gets the widgets from the model passed to it ex:Role_widget or Admin_widget
	 * Also passing a condition like checking on the admin_id or role_id
	 * @param $model
	 * @return mixed
	 */
	public static function getWidgetData( $model, $condition = [] )
	{
		$routeName = \Request::route()->getName();
		// If route has a . then get all text after the .
		$routeName = substr($routeName, strpos($routeName, '.') !== false ? strpos($routeName, '.') +1 : 0);

		$siteType = session('admin.site.type');

		$widgetsData = $model::with(['widget' => function ($query) use ($routeName, $siteType){
			$query->where('status', '!=', 'inactive')
				->where('type', $siteType);
			}]);

		if(!empty($condition))
			$widgetsData = $widgetsData->where($condition);

		$widgetsData = $widgetsData->where('route', $routeName)
			->orderBy('order', 'asc')
			->select('widget_id', 'order', 'status')
			->get()
			->keyBy('widget_id');

		// Do a filter on each item in the result collection, where it removes the widget status id
		$widgetsData = $widgetsData->map(function ($item, $key) use ($routeName) {
			if(is_null($item->widget))
				return null;

			if(strpos($item->widget->routes, $routeName) === false)
				return null;

			$widgetsArray = $item->widget->toArray();

			unset($item->widget, $item->widget_id, $widgetsArray['status']);
			return $widgetsArray + $item->toArray();
			});

		return $widgetsData;
	}


	/**
     * Save widget order and status
     * Loops through all widgets that user can see, compares with all current widgets based on siteType
     * any widgets that are not in the all widgets list are set in the DB as inactive and the others are ordered
     * using the javascript data passed into $request
     * @param $request
     */
    static function saveWidgetOrder($request)
    {
        // get site type from session
        $siteType = session('admin.site.type');

        // Get routename in same way as other methods
		$routeName = $request->route()->getName();
		//$routeName = $request->route;

		// If there is no list of allowed widgets in the session,
		// then get it from the db and store it in the session
		$sessionKey = 'admin.user.allowed_widgets.' . $siteType . '_' . $routeName;
		if ( ! \Session::has( $sessionKey ) ||  empty(session( $sessionKey )) ) {
			//This method will populate the session key
			self::getAllowedWidgets();
		}

		$allowedWidgetIds = session($sessionKey);

        $widgetData = [];

        // Go through allowed widgets
        foreach ($allowedWidgetIds as $allowedWidgetId) {

			$key = false;
        	if(!empty($request->widgetList))
				// search the $allWidgets array looking for any 'widget_id' that matches $allowedWidget['widget_id]
				$key = array_search($allowedWidgetId, array_column($request->widgetList, 'widget_id'));

            //if any current widgets widget_id does not match $allowedWidget's widget_id
            if($key === false)
                $widgetData[] = [
                    'admin_id'   => $request->user_id,
                    'widget_id'  => $allowedWidgetId,
                    'order'      => 0,
                    'route'      => $request->route,
                    'status'     => 'inactive'
                ];
            else
                $widgetData[] = [
                    'admin_id'   => $request->user_id,
                    'widget_id'  => $request->widgetList[$key]['widget_id'],
                    'order'      => $request->widgetList[$key]['order'],
                    'route'      => $request->route,
                    'status'     => $request->widgetList[$key]['status']
                ];
        }

        // Delete all widget data
        AdminWidget::whereHas('widget', function ($query) use ($siteType, $routeName){
        	// Retrieving widgets with the same type as the current sitetype
				$query = $query->where('type', $siteType);
				return $query;
			})
			->where(['admin_id' => $request->user_id, 'route' => $request->route])
			->delete();

       // insert all widgets
        AdminWidget::insert($widgetData);

        //this creates a message to say that the user has edited their dashboard
        Messages::create(Messages::SUCCESS_MSG, session('admin.user.username') .' ' . trans('admin.saved-dashboard'), false);

    }

/*	Not Used for now to update only 1 widget status
	static function updateWidget($request)
	{
		AdminWidget::updateOrCreate(
			['admin_id' => $request->user_id, 'route' => $request->route, 'widget_id' => $request->widget_id],
			['status' => $request->status, 'order' => $request->order]
		);
	}
*/
}