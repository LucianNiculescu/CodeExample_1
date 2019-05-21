<?php

namespace App\Admin\Modules\Users;

use Request;
use App\Admin\Modules\Templates\Logic as Templates;
use App\Models\AirConnect\Translation;
use App\Helpers\DateTime;
use App\Admin\Modules\Sites\Logic as Sites;
use App\Admin\Modules\Roles\Logic as Roles;
use \App\Models\AirConnect\Admin as AdminModel;

/**
 * Class SetupViewData
 * @package App\Admin\Modules\Vouchers
 */
class SetupViewData {
	/**
	 * Set up the DataTable view (server/client side, based on the requested path)
	 * @return array
	 */
	public static function getTableData() {
		if(\Request::path() == 'users') {
			$usersDatatable = Datatable::getTable();
			$title 			= trans('admin.users-title');
			$description 	= trans('admin.users-desc');
		} else { // system/users
			$usersDatatable = Datatable::getTable(true);
			$title 			= trans('admin.system-users-title');
			$description 	= trans('admin.system-users-desc');
		}

		$data = [
			'title' 			=> $title,
			'description'		=> $description,
			'usersDatatable'	=> $usersDatatable,
		];

		return $data;
	}

    /**
	 * Setting up the create voucher view
	 * title, description, voucherTypes, siteLocation ...etc
     * @return array
     */
    public static function create()
    {
		// Setup the form's action and method
		$actionUrl = '/' . str_replace ('/create','',\Request::path());
		$hiddenMethod = 'POST';

		// Data sent to the create page
		$data = [
			'title' 		=> trans('admin.new-user-title') ,
			'description' 	=> trans('admin.new-user-desc'),
			'hiddenMethod' 	=> $hiddenMethod ,
			'actionUrl' 	=> $actionUrl
		];

		return $data + self::commonSetup($actionUrl);
    }

	/**
	 * Set up the Edit users view
	 * @param $id
	 * @return array
	 */
    public static function edit($id) {
		// Setup the form's action and url
		$actionUrl = '/' . str_replace ('/edit','',\Request::path());
		$hiddenMethod = 'PUT';

		//Get this Admin
		$user = AdminModel::find($id);

		// Data to be sent to the Role edit page
		$data =
			[
				'title' 		=>  trans('admin.edit-user-title') ,
				'description' 	=>  trans('admin.edit-user-desc'),
				'module'	    => $user ,
				'hiddenMethod' 	=> $hiddenMethod ,
				'actionUrl' 	=> $actionUrl,
				'password'      => substr($user->password,0,7),
				'edit'          => true,
			];


		return $data + self::commonSetup($actionUrl);
    }

    public static function commonSetup($actionUrl)
	{
		if (strpos($actionUrl, 'system') === false)
			$systemPage = false;
		else
			$systemPage = true;

		$templates  = Templates::getTemplates();
		$languages  = Translation::getLanguages('admin');
		$timeZones  = DateTime::getTimeZones();

		// If it is all Users , then setup the sites dropdownlist with  name(type) in the $site[0] and the location in $site[1]
		if($systemPage) {
			// All users will send all sites from the estate to fill the drop downlist
			$sites  = Sites::fillSitesList(session('admin.site.estate'));
			// All Roles that in this specific estate
			$roles  = Roles::getRolesForUsers(session('admin.user.role_id'), $systemPage);
			$data['route'] = 'system/users';
		} else {
			// All users will send all sites to fill the drop downlist
			$sites  = Sites::fillSitesList();
			// All Roles that user can manage
			$roles  = Roles::getRolesForUsers(session('admin.user.role_id'));
			$data['route'] = 'users';
		}

		return $data + [
			'templates' => $templates,
			'languages' => $languages,
			'timeZones' => $timeZones,
			'sites'     => $sites,
			'roles'     => $roles,
		];
	}
}