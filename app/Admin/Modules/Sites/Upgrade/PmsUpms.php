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
class PmsUpms extends Pms
{
	/**
	 * The base class for actions which must be performed as part of upgrading the system
	 * The class
	 * @param $pmsType
	 * @param $lowVersion
	 * @param $highVersion
	 * @param $site
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
	 * For upms we need to add a level and store the pms type as upms and the v1 pms type as the upms type
	 * @return bool
	 */
	public function up()
	{
		// Get the v1 upms information
		$v1Attributes = PortalAttributeModel::where([
			['type', 'upms'],
			['ids', $this->site],
			['status', 'active']
		])
			->select('name', 'value')
			->get();

		// Transform the collection of name value pairs to an associative array with name as key
		$fromArray = [];
		foreach ($v1Attributes as $v1Attribute) {
			$fromArray[$v1Attribute->name] = $v1Attribute->value;
		}

		/**
		 * The upms settings are stored in the site attributes in both v1 and v3 but there are
		 * some differences.
		 * Unchanged: 'port', 'protocol';
		 * Changed name: 'ip' becomes 'uri';
		 * Removed : 'failover';
		 * Added :	'id'		- default to site id,
		 *			'override'	- default to false.
		 */

		// Update the 'uri' attribute to 'ip'
		SiteAttributeModel::where(
			[
				['ids', "=", $this->site],
				['name', "=", 'ip'],
				['type', "=", 'upms']
			])
			->update(['name'	=> 'uri']);

		//Remove the 'failover' attribute
		SiteAttributeModel::where(
			[
				['ids', "=", $this->site],
				['name', "=", 'failover'],
				['type', "=", 'upms']
			])
			->delete();

		// Insert the new site attributes for pms
		SiteAttributeModel::insert(
			[
				[
					'ids'		=> $this->site,
					'name'		=> 'pms_type',
					'type'		=> 'pms',
					'status'	=> 'active',
					'value'		=> 'upms'
				],
				[
					'ids'		=> $this->site,
					'name'		=> 'upms_type',
					'type'		=> 'upms',
					'status'	=> 'active',
					'value'		=> $this->pmsType
				],
				[
					'ids'		=> $this->site,
					'name'		=> 'id',
					'type'		=> 'pms',
					'status'	=> 'active',
					'value'		=> $this->site
				],
				[
					'ids'		=> $this->site,
					'name'		=> 'override',
					'type'		=> 'upms',
					'status'	=> 'active',
					'value'		=> 'false'
				]
			]);

		return true;
	}


	/**
	 * On v1, PMS authentication type is stored in the portal attributes
	 * For v3, it is stored in the site attributes
	 * To revert (roll back), the best we can do is take the PMS information from the site and create attributes for it against an active portal
	 * In the absence of further information, we will take the first that has no pms type
	 * Note that we cannot recover the 'failover' site attribute for v1 and will lose the 'id' and 'override' site attributes for v3.
	 * @return bool
	 */
	public function down()
	{
		// Get the "upms" site attributes from v3
		$v3Attributes = SiteAttributeModel::where(
			[
				['ids', '=', $this->site],
				['type', '=', 'upms'],
				['status', '=', 'active']
			])
			->select('name', 'value')
			->get();

		// Transform the collection of name value pairs to an associative array with name as key
		$fromArray = [];
		foreach ($v3Attributes as $v3Attribute) {
			$fromArray[$v3Attribute->name] = $v3Attribute->value;
		}

		// We store the upms type as the pms type on v1
		$portalAttributes = [
				'ids'		=> $this->portal->id,
				'name'		=> 'pms_type',
				'status'	=> 'active',
				'type'		=> 'authentication',
				'value'		=> $fromArray['upms_type']
			];

		// We need to transform the v3 upms attributes to the v1 form.
		// What needs changing is described in the "up" processing and here we reverse that.
		// Update the 'ip' attribute to 'uri'
		SiteAttributeModel::where(
			[
				['ids', "=", $this->site],
				['name', "=", 'uri'],
				['type', "=", 'upms']
			])
			->update(['name'	=> 'ip']);

		// Add the 'failover' attribute and default it to false
		SiteAttributeModel::insert(
			[
				'ids'		=> $this->site,
				'name'		=> 'failover',
				'type'		=> 'upms',
				'value'		=> 'false'
			]);

		// Remove the 'override' attribute
		SiteAttributeModel::where(
			[
				['ids', "=", $this->site],
				['name', "=", 'override'],
				['type', "=", 'upms']
			])
			->delete();

		// Remove the 'id' attribute
		SiteAttributeModel::where(
			[
				['ids', "=", $this->site],
				['name', "=", 'id'],
				['type', "=", 'pms']
			])
			->delete();

		// We also need to delete the upms type.
		SiteAttributeModel::where(
			[
				['ids', "=", $this->site],
				['name', "=", 'upms_type'],
				['type', "=", 'upms'],
				['status', "=", 'active']
			])
			->delete();

		// Create the PMS type in the attributes for the portal
		PortalAttributeModel::insert($portalAttributes);

		return true;
	}

}