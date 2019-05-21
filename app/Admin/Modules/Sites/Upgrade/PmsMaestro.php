<?php
/**
 * Created by Airangel.
 * User: Nigel Whitley
 * Date: 06-Oct-17
 * Time: 10:35 AM
 */

namespace App\Admin\Modules\Sites\Upgrade;

use App\Models\AirConnect\Site as SiteModel;
use App\Models\AirConnect\Gateway as GatewayModel;
use App\Models\AirConnect\Portal as PortalModel;
use App\Models\AirConnect\PortalAttribute as PortalAttributeModel;
use App\Models\AirConnect\SiteAttribute as SiteAttributeModel;
use App\Models\Maestro\Company as MaestroModel;
use App\Models\PMS\Xsg as XsgModel;

/**
 * Class for actions which must be performed to upgrade PMS settings
 */
class PmsMaestro extends Pms
{
	protected $pmsType;

	/**
	 * The base class for upgrading Pms settings for a site
	 * The class
	 * @param $site
	 * @param $lowVersion
	 * @param $highVersion
	 */
	function __construct($pmsType, $site, $lowVersion = 1, $highVersion = 3)
	{
		parent::__construct($site, $lowVersion, $highVersion);
		$this->pmsType	= $pmsType;
	}


	/**
	 * At present we only support upgrade from v1 to v3
	 * On v1, PMS authentication type is stored in the portal attributes
	 * For v3, it is stored in the site attributes
	 * In theory, we can have several different PMS types stored against a site on v1 but we can only have one on v3.
	 * We will only take the type from the active portal and use that for the site
	 * @return bool
	 */
	public function up()
	{
		// For maestro we need to create an 'id' which is the id of the company row in the maestro database
		$company = MaestroModel::where('site', $this->site)->first();

		SiteAttributeModel::insert(
			[
				'ids'		=> $this->site,
				'name'		=>'id',
				'type'		=> 'pms',
				'status'	=> 'active',
				'value'		=> $company->id
			]);

		// Insert the new site attributes for pms
		SiteAttributeModel::insert(
			[
				'ids'		=> $this->site,
				'name'		=>'pms_type',
				'type'		=> 'pms',
				'status'	=> 'active',
				'value'		=> $this->pmsType
			]);

		return true;
	}


	/**
	 * On v1, PMS authentication type is stored in the portal attributes
	 * For v3, it is stored in the site attributes
	 * To revert (roll back), the best we can do is take the PMS information from the site and create attributes for it against an active portal
	 * @param $portal -  the portal to which we add an attribute for the pms type
	 * @return bool
	 */
	public function down()
	{
		// We need to delete the 'id' attribute
		// We do the same for micros although the id points to a different table
		SiteAttributeModel::where(
			[
				['ids', $this->site],
				['name', 'id'],
				['type', 'pms'],
				['status', 'active']
			])
			->delete();

		// Create the PMS type in the attributes for the portal
		PortalAttributeModel::insert(
			[
				'ids'		=> $this->portal->id,
				'name'		=> 'pms_type',
				'status'	=> 'active',
				'type'		=> 'authentication',
				'value'		=> $this->pmsType
			]);

		return true;
	}

}