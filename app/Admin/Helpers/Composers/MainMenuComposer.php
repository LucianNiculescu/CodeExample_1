<?php

namespace App\Admin\Helpers\Composers;

/**
 * Creates the main menu
 * Class MainMenuComposer
 * @package App\Admin\Helpers\Composers
 */
class MainMenuComposer {

	private $menu;
	private $defaultMenuItems 	= ['estate', 'system', 'help', 'search'];
	private $noPermissionsItems = ['dashboard', 'estate', 'help', 'search'];

    public function compose($view)
    {
		if (\Cache::has('admin.main-menu'))
		{
			$this->menu = \Cache::get('admin.main-menu');
		}
		else
		{
			$this->setupMenu();
			\Cache::put('admin.main-menu', $this->menu);
		}

		// Setting the right menu item that has the Active CSS Class
		$this->settingActiveItem();

        $view->with('menu',  $this->menu);
    }


    /**
     * Set the menu array
     * NB. Make sure the permissions are set up or you won't see the links!
     */
    private function setupMenu()
    {
		// Initiating the main menu
		$this->initiateMenu();

		// Adjusting the main menu depending on permission and if user logged in to a site or not and also the active class
		$this->prepareMenu();
	}

	/**
	 * Strip from the Menu by the site type
	 * @param string $item
	 * @param array $settings
	 */
	private function stripByType( string $item, array &$settings)
	{
		// If we have type/s we only show for
		if( isset($settings['type']) && is_array($settings['type']))

			// If we don't show on this type, unset it
			if(!in_array( session('admin.site.type'), $settings['type']))

				// We can use dot notation here
				array_forget($this->menu, $item);
	}

	/**
	 * Preparing menu to show the default items if user is not logged in
	 * Also it checks the permission
	 */
	private function prepareMenu()
	{
		// Looping in the menu and passing the setting by reference in order to remove some submenus if needed
		foreach ($this->menu as $item => &$settings)
		{
			// Don't show if it is not of this type
			$this->stripByType( $item, $settings );

			// Showing only default Menu items if user isn't logged in to a site
			$this->checkDefault($item );

			// Remove the estate menu item if there is a site logged in the session and there are no other sites in the estate
			if($item ==='estate' && is_array(session('admin.site.estate')) && count(session('admin.site.estate')) === 1)
				array_forget($this->menu, $item);

			// Checking Permissions and the types
			if( isset($settings['links']) && !empty($settings['links']))
			{
				foreach ($settings['links'] as $subitem => &$subSettings)
				{
					// Remove the Vouchers item if there are no Voucher packages
					if($subitem === 'vouchers' && !in_array('voucher', session('admin.site.active_package_types', [])))
						array_forget($this->menu, $item.'.links.'.$subitem);

					// Check if the Adjets are activated on this site or remove them from the menu
					if($subitem === 'adjets')
						if(!\App\Admin\Modules\AdJets\Logic::checkEnabledAdJets())
							array_forget($this->menu, $item.'.links.'.$subitem);

					// Don't show if it is not of this type
					$this->stripByType( $item.'.links.'.$subitem, $subSettings );

					//
					$this->checkPermission($settings['links'], $subitem, $subSettings);
				}

				// If there is nothing in the submenus then remove the Menu Item
				if(count($settings['links']) == 0)
				{
					array_forget($this->menu, $item);
				}
			}
			else
			{
				$this->checkPermission($this->menu, $item, $settings);
			}
		}
	}


	/**
	 * Reseting all active settings to false and setting the right active property to true
	 */
	private function settingActiveItem()
	{
		// Looping in the menu and resetting the active setting to false
		foreach ($this->menu as $item => &$settings)
		{
			if( isset($settings['links']) && !empty($settings['links']))
			{
				$settings['active'] = false;

				foreach ($settings['links'] as $subitem => $subSettings)
				{
					$subSettings['active'] = false;
				}
			}
			else
			{
				$settings['active'] = false;
			}
		}

		// $url is the route is
		$url = '/'. \Request::path();

		// Looping in the menu and setting the active property
		foreach ($this->menu as $item => &$settings)
		{
			if( isset($settings['links']) && !empty($settings['links']))
			{
				foreach ($settings['links'] as $subitem => &$subSettings)
				{
					// If the route equals the menu sub item url, then it should be active and the main menu item too
					if(substr($url, 0, strlen($subSettings['url'])) === $subSettings['url'])
					{
						$settings	['active'] 	= true;
						$subSettings['active'] 	= true;
					}
				}
			}
			else
			{
				// If the route equals the menu item url, then it should be active

				if(substr($url, 0, strlen($settings['url'])) === $settings['url'])
				{
					$settings['active'] = true;
				}
			}
		}
	}


	/**
	 * Checking the permission if user is not allowed to see the menu item it will be removed from the list
	 * @param $menu
	 * @param $item
	 * @param $settings
	 */
	private function checkPermission(&$menu, $item, $settings)
	{
		// this is a developer then escape any checking
		if (auth()->user()->role_id == 0)	// was adminId
			return ;

		// If item doesn't need permission i.e. in the no PermissionsItems array then skip it
		if(in_array($item, $this->noPermissionsItems))
			return;

		// Removing the first / from the url
		$url = substr($settings['url'], 1);

		// Replacing / with .
		$permission = str_replace('/', '.', $url);
		// Replacing .:. with . for exceptional routes like manage/sites/:/edit
		$permission = str_replace('.:.', '.', $permission);

		// If the user doesn't have the permission then remove the item from the menu
		if (!auth()->user()->hasPermission($permission))
		{
			array_forget($menu, $item);
		}
	}

	/**
	 * If user is not logged in to a site there only few menu items to show
	 * They are listed in defaultMenuItems
	 * @param $item
	 */
	private function checkDefault($item)
	{
		if(is_null(session('admin.site.loggedin')))
		{
			if (!in_array($item, $this->defaultMenuItems))
			{
				array_forget($this->menu, $item);
			}
		}
	}


    /**
     * Set the menu array and save it in the session
     * NB. Make sure the permissions are set up or you won't see the links!
     */
    private function initiateMenu()
    {
		$this->menu = config('menu.admin.main');
    }
}