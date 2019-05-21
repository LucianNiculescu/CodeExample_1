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
class Pms extends SiteUpgrade
{
	protected $pmsType;
	protected $portal;

	/**
	 * The base class for upgrading Pms settings for a site
	 * The class
	 * @param $site
	 * @param $lowVersion
	 * @param $highVersion
	 */
	function __construct($site, $lowVersion = 1, $highVersion = 3)
	{
		parent::__construct($site, $lowVersion, $highVersion);
	}


	/**
	 * Return the upgrade object for the type of PMS
	 * @return PmsMaestro|PmsMicros|PmsUpms|null
	 */
	public function getPmsObject()
	{
		// Check whether it is upms or one of the subtypes of the 'upms' type in v3
		if (in_array($this->pmsType, ['upms','oracle', 'vinn', 'itesso'])) {
			$subType = new PmsUpms($this->pmsType, $this->site, $this->lowVersion, $this->highVersion);
		} elseif ($this->pmsType === 'maestro') {
			$subType = new PmsMaestro($this->pmsType, $this->site, $this->lowVersion, $this->highVersion);
		} elseif ($this->pmsType === 'micros') {
			$subType = new PmsMicros($this->pmsType, $this->site, $this->lowVersion, $this->highVersion);
		} else {
			//Unknown PMS type
			return null;
		}
		return $subType;
	}


	/**
	 * Set the portal to which the pms type attribute must be attached when downgrading.
	 * This is used for the types which inherit from Pms.
	 * @return PmsMaestro|PmsMicros|PmsUpms|null
	 */
	public function setPortal($portal)
	{
		$this->portal = $portal;
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
		// Get the pms type from the active portal.
		// If we don't have an active portal or don't have a pms type then there is nothing to do
		$pmsAttribute = PortalAttributeModel::whereHas('portal',
			function ($query)  {
				$query->where([
					['site', $this->site],
					['status', 'active']
				]);
			})
			->where(
				[
					['type', 'authentication'],
					['name', 'pms_type'],
					['status', 'active']
				])
			->first();

		if (is_null($pmsAttribute)) {
			//No pms type for active portal so don't try to migrate the PMS settings
			\Log::info('No pms settings to migrate');
			return true;
		}

		// Get the Pms type from the portal attribute
		$this->pmsType = $pmsAttribute->value;
		// Get the PMS type-specific object
		$subType = $this->getPmsObject();
		// If we don't have an object then we cannot transfer the settings so the upgrade fails
		if ( is_null($subType) ) {
			return false;
		}

		// Try the upgrade with the type-specific object
		if ( $subType->up() ) {
			/**
			 * Delete the PMS info we used from the portal attributes
			 * If there are multiple portals with pms set we should only delete the one we used.
			 * This protects against throwing away useful information but may leave clutter.
			 * Perhaps there needs to be a "cleanup" task which clears out pms info for the other portals
			 * when the upgrade has been deemed a success.
			 */
			$pmsAttribute->delete();
			return true;
		} else {
			return false;
		}

	}


	/**
	 * On v1, PMS authentication type is stored in the portal attributes
	 * For v3, it is stored in the site attributes
	 * To revert (roll back), the best we can do is take the PMS information from the site and create attributes for it against the active portal
	 * Note that we cannot recover the 'failover' site attribute for v1 and will lose the 'id' and 'override' site attributes for v3.
	 * @return bool
	 */
	public function down()
	{
		/**
		 * Get the pms type from the site attributes
		 */
		$pmsAttribute = SiteAttributeModel::where(
			[
				['ids', $this->site],
				['name', 'pms_type'],
				['type', 'pms'],
				['status', 'active']
			])
			->first();

		// There should only ever be one PMS type defined in the site attributes or none.
		// If there is no PMS type then we have nothing more to do here.
		// We return true because it's OK to have no PMS settings.
		if (empty($pmsAttribute))
			return true;

		// Find a candidate portal for storing the PMS info.
		// Use the active portal if it has no pms_type
		$this->portal = PortalModel::where(
			[
				['site', $this->site],
				['status', 'active']
			])
			->whereNotExists(function ($query) {
				$query->select(\DB::raw(1))
					->from('portal_attribute')
					->where(
						[
							['name', 'pms_type'],
							['type', 'authentication'],
							['status', 'active']
						])
					->whereRaw('ids = portal.id');
			})
			->first();

		// Have we found a suitable portal?
		if (is_null($this->portal)) {
			\Log::info('No portal available');
			// We need to create a portal attribute for the PMS type but there is no portal so we fail
			return false;
		}

		// Get the Pms type from the portal attribute
		$this->pmsType = $pmsAttribute->value;
		// Get the PMS type-specific object
		$subType = $this->getPmsObject();
		// If we don't have an object then we cannot transfer the settings so the downgrade fails
		if (is_null($subType)) {
			return false;
		}

		$subType->setPortal($this->portal);

		// Try the downgrade with the type-specific object.
		if ( $subType->down() ) {
			// As with the upgrade, we delete the pms_type information
			$pmsAttribute->delete();
			return true;
		} else {
			return false;
		}
	}

}