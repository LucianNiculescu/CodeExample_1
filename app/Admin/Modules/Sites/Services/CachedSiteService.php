<?php

namespace App\Admin\Modules\Sites\Services;

use App\Admin\Modules\Sites\CachedSite;
use App\Models\AirConnect\Site;
use Illuminate\Auth\AuthManager as Auth;
use Illuminate\Cache\CacheManager as Cache;
use Illuminate\Session\SessionManager as Session;

/**
 * Class responsible for provisioning CachedSite classes
 */
class CachedSiteService
{
	/**
	 * @var Auth
	 */
	private $auth;

	/**
	 * @var Cache
	 */
	private $cache;

	/**
	 * @var Session
	 */
	private $session;

	/**
	 * Whether the Service has been initialised
	 *
	 * @var bool
	 */
	private $init = false;

	/**
	 * @var array $cachedSites Array of CachedSite models by their ID
	 */
	private $cachedSites;

	/**
	 * The namespace in which cache keys are defined in
	 *
	 * @var string
	 */
	private $cacheNamespace = 'admin.site';

	/**
	 * The namespace in which session keys are defined in
	 *
	 * @var string
	 */
	public $sessionNamespace = 'admin.site';

	/**
	 * The largest number of Sites in a Path, stored in cache and updated every time
	 * a Site is cached
	 *
	 * @var int
	 */
	public $largestPathLength = 0;

	/**
	 * Class constructor
	 *
	 * @param Auth $auth
	 * @param Cache $cache
	 * @param Session $session
	 */
	public function __construct(Auth $auth, Cache $cache, Session $session)
	{
		// Initialize class variables
		$this->cache = $cache;
		$this->session = $session;
		$this->auth = $auth;
	}

	/**
	 * Initialize the class. If a logged in Site ID is set in Session, load cached data.
	 *
	 * Called when resolving the Service, required as opposed to calling from the constructor
	 * due to the authenticated user only being resolved once HTTP middleware has been executed
	 */
	public function init()
	{
		// If we are already initialized, return
		if ($this->init)
			return true;

		// Get the largest Site path from Cache
		$this->largestPathLength = $this->cache
			->rememberForever(CachedSite::$cacheNamespace . '.largest_path', function () {
				// Set at 1 so that when the Top Level Site is cached, the accurate largest path is stored
				return 1;
			});

		// Ensure the Top Level Site is cached before all others to build an accurate largest path after cache clear
		if(!CachedSite::exists(1))
			$this->loadCachedSite( 1 );

		// If there is no logged in Site in session, use the User's Site
		$siteId = $this->getLoggedInSiteId() === false
			? $this->auth->user()->site
			: $this->getLoggedInSiteId();

		// Load the CachedSite class, which also hydrates and validates the Site's cache
		$this->loadCachedSite( $siteId );

		$this->init = true;
	}

	/**
	 * Get or update the largest path length on the class
	 *
	 * @param  bool|int $length
	 * @return int
	 */
	public function largestPathLength($length = false)
	{
		// No length given to update, return what is stored on the class
		if($length === false)
			return $this->largestPathLength;

		// Update length in cache and on the Service
		$this->cache->forever(CachedSite::$cacheNamespace . '.largest_path', $length);
		$this->largestPathLength = $length;

		return $length;
	}

	/**
	 * Loads a CachedSite class by a Site's ID
	 *
	 * @param  int|Site $site
	 * @return void
	 */
	private function loadCachedSite( $site )
	{
		// Get the Site ID
		$siteId = $site instanceof Site ? $site->id : $site;

		// Store the CachedSite instance on the class
		$this->cachedSites[$siteId] = CachedSite::load( $site );

		// Update our application path length
		$this->trackPathLength( $this->cachedSites[$siteId]->path()->count() );
	}

	/**
	 * Gets a CachedSite class from those already loaded by the Service, or loads
	 *
	 * @param  int|Site $site
	 * @return CachedSite
	 */
	public function getCachedSite( $site ) : CachedSite
	{
		// Get the Site ID
		$siteId = $site instanceof Site ? $site->id : $site;

		if(!isset($this->cachedSites[$siteId]))
			$this->loadCachedSite($site);

		return $this->cachedSites[$siteId];
	}

	/**
	 * Whether the class has the cached site stored
	 *
	 * @param  int|Site $site
	 * @return bool
	 */
	public function hasCachedSite($site)
	{
		// Get the Site ID
		$siteId = $site instanceof Site ? $site->id : $site;

		return isset($this->cachedSites[$siteId]);
	}

	/**
	 * Ensures the Cache var largest_path always has the largest path of any
	 * Site cached
	 *
	 * @param int $length
	 */
	private function trackPathLength($length)
	{
		if ($length > $this->largestPathLength)
			$this->largestPathLength($length);
	}

	/**
	 * Returns the User's CachedSite class
	 *
	 * @return CachedSite
	 */
	public function userCachedSite() : CachedSite
	{
		$userSiteId = $this->auth->user()->site;

		return $this->getCachedSite($userSiteId);
	}

	/**
	 * Returns the logged in Site's CachedSite class
	 *
	 * @return bool|CachedSite
	 */
	public function loggedInCachedSite()
	{
		if(!$this->getLoggedInSiteId())
			return false;

		return $this->getCachedSite($this->getLoggedInSiteId());
	}

	/**
	 * Get the Cache key for the Site
	 *
	 * @param  int $siteId
	 * @return string
	 */
	public function getCacheKey($siteId)
	{
		return CachedSite::$cacheNamespace . '.' . $siteId;
	}

	/**
	 * Gets the SiteID stored in Session which the User is logged into
	 *
	 * @return int|bool
	 */
	public function getLoggedInSiteId()
	{
		$loggedInSessionKey = $this->sessionNamespace . '.loggedin';

		return $this->session->has($loggedInSessionKey)
			? $this->session->get($loggedInSessionKey)
			: false;
	}


}