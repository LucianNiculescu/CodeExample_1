<?php
/**
 * Created by Airangel.
 * User: Nigel Whitley
 * Date: 06-Oct-17
 * Time: 10:35 AM
 */

namespace App\Admin\Modules\Sites\Upgrade;

/**
 * The base class for actions which must be performed as part of upgrading the system
 * The class supports upgrading and downgrading, although some actions may not be fully reversible.
 * At present, we only anticipate support for upgrading from v1 to v3 but storing the version numbers allows flexibility for the future.
 */
class SiteUpgrade
{
	protected $site;
	protected $lowVersion;
	protected $highVersion;

	/**
	 * The base class for actions which must be performed as part of upgrading the system
	 * The class
	 * @param $site
	 */
	function __construct($site, $lowVersion = 1, $highVersion = 3)
	{
		$this->site	= $site;
		$this->lowVersion = $lowVersion;
		$this->highVersion = $highVersion;
	}


	/**
	 * Upgrade from $lowVersion to $highVersion returning an indication of whether it was successful.
	 * A successful upgrade is when all actions which need to be taken have been taken.
	 * When there is nothing more to do then the upgrade is successful and must return true.
	 * The function must return false if the upgrade will not succeed.
	 * @return bool
	 */
	public function up()
	{
		return true;
	}


	/**
	 * Downgrade to $lowVersion from $highVersion returning an indication of whether it was successful.
	 * A successful downgrade is when all actions which need to be taken have been taken.
	 * When there is nothing more to do then the downgrade is successful and must return true.
	 * The function must return false if the downgrade will not succeed.
	 * @return bool
	 */
	public function down()
	{
		return true;
	}

}