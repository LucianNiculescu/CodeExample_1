<?php

namespace App\Admin\Helpers\Composers;

use App\Models\AirConnect\AdminWidget;

class WidgetMenuComposer {

    public function compose($view)
    {
        //$view->with('inactive_widgets',  $this->getInactiveWidgets());
    }

    /**
     * Get Template
     * runs the setTemplate function and returns the template name to be composed into the view
     * @return string
     *
     */
    //
    public function getInactiveWidgets()
    {
        // gets admin user id
        $adminId = session('admin.user.id');
		$routeName = \Request::route()->getName();
		// If route has a . then get all text after the .
		$routeName = substr($routeName, strpos($routeName, '.') !== false ? strpos($routeName, '.') +1 : 0);
        // site, company or estate
        $siteType = session('admin.site.type');
        // TODO: look at this query and figure out if it needs to check the role widget table too
        // whereHas gets the widget status from the widget table
        return AdminWidget::whereHas('widget', function ($query) use ($siteType){
            return $query->where(['status' => 'active', 'type' => $siteType]);
        })
            ->where('admin_id', $adminId)
            ->where('status', 'inactive')
            ->where('route', $routeName)
            ->orderBy('order', 'asc')
            ->get();
    }

}