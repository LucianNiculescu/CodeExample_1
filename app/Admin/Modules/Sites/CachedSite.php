<?php

namespace App\Admin\Modules\Sites;

use App\Models\AirConnect\Site;
use App\Transformers\SiteTransformer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Class responsible for hydrating, validating, and providing a Site's cached data
 * to the rest of the application. Simply load by a Site's ID for automatically cached
 * data:
 *
 * <code> $cachedSite = CachedSite::loadById( $siteId ); </code>
 *
 * @link https://github.com/airangel/myairangel-v3/wiki/Site-Caching#cachedsite
 */
class CachedSite
{
	/**
	 * The namesapce to store cached Site data
	 *
	 * @var string
	 */
	public static $cacheNamespace = 'admin.site';

	/**
	 * The Site loaded by the class
	 *
	 * @var Site
	 */
	protected $site;

	/**
	 * The Site's cached data
	 *
	 * @var array
	 */
	protected $attributes;

	/**
	 * Instantiate the class by a Site's ID
	 * Returns from the CachedSiteService container if available
	 *
	 * @param  int|Site $site
	 * @return $this
	 */
	public static function load( $site )
	{
		// Check whether the Service already had the requesting CachedSite
		if(cached_site_service()->hasCachedSite($site))
			return cached_site_service()->getCachedSite($site);

		$self = new self();
		$self->site = $site instanceof Site ? $site : Site::findOrFail( $site );
		$self->loadCachedData( $self->site );
		return $self;
	}

	/**
	 * Checks whether cached data exists for the given Site ID
	 *
	 * @param  int $id
	 * @return bool
	 */
	public static function exists( $id )
	{
		// Get the cache key
		$cacheKey = self::$cacheNamespace . '.' . $id;
		
		return Cache::has($cacheKey);
	}

	/**
	 * Get the Site eloquent model for this CachedSite
	 *
	 * @return Site
	 */
	public function site() : Site
	{
		return $this->site;
	}

	/**
	 * Get the Site type
	 *
	 * @return null|string
	 */
	public function type()
	{
		return $this->getAttributeValue('attributes')
			? $this->getAttributeValue('attributes')['sitetype']
			: null;

	}

	/**
	 * Get a unique list of package types available on the Site
	 *
	 * @return null|Collection
	 */
	public function packageTypes()
	{
		return $this->getAttributeValue('package_types');
	}

	/**
	 * Get the Site path (to the very first parent)
	 *
	 * @return null|Collection
	 */
	public function path()
	{
		return $this->getAttributeValue('path');
	}

	/**
	 * Returns the Site path to it's closest Estate
	 *
	 * @return null|Collection
	 */
	public function pathToEstate()
	{
		if(is_null($path = $this->path()))
			return null;

		$estateFound = false;
		return $path->reject(function($site) use (&$estateFound) {

			// If the Site is an estate, set the flag and return false to include in path
			if(!$estateFound and (is_null($site['type']) or $site['type'] == 'estate'))
			{
				$estateFound = true;
				return false;
			}

			// Reject if estate has been found
			return $estateFound;
		});
	}

	/**
	 * Returns the path for the Site up to the User's Site
	 *
	 * @return null|Collection
	 */
	public function pathForUser()
	{
		if(is_null($path = $this->path()))
			return null;

		$userSiteId = auth()->user()->site;

		$userSiteFound = false;
		return $path->reject(function($site) use ($userSiteId, &$userSiteFound) {

			// If the Site matches the User's site ID, set the flag to false to include in path
			if(!$userSiteFound and $site['id'] == $userSiteId)
			{
				$userSiteFound = true;
				return false;
			}

			// Reject if user's site has been found
			return $userSiteFound;
		});
	}

	/**
	 * Get the children for the Site (all child Sites and their children)
	 *
	 * @return null|Collection
	 */
	public function children()
	{
		return $this->getAttributeValue('children');
	}

	/**
	 * Return the Site's estate (the Site's children, and the Site)
	 *
	 * @return null|Collection
	 */
	public function estate()
	{
		if(is_null($children = $this->children()))
			return null;

		$formattedSite = [
			'id' 	    => $this->site['id'],
			'type' 	    => is_null($this->site['type']) ? 'site' : $this->site['type'],
			'name' 	    => $this->site['name'],
            'status'    => $this->site['status'],
		];

		return collect([$formattedSite])->merge($children);
	}

	/**
	 * Load and validate the Site's cached data on the class
	 *
	 * @param  Site $site
	 * @return void
	 */
	private function loadCachedData(Site $site)
	{
		// Get the key to load from the cache
		$cacheKey = self::$cacheNamespace . '.' . $this->site->id;

		// Retrieve the item from cache, or set the value if it doesn't exist
		$this->attributes = Cache::rememberForever($cacheKey, function () use (&$site) {

			// Eager load the Site relations needed for storing
			$site->load('attributes', 'packages');

			// Store the required data from the Site in the Cache
			return SiteTransformer::transform($site)->into('cache');
		});

		// If the Cached data is invalid, refresh
		if(!$this->isCacheValid())
		{
			// Destroy and rehydrate the cached data
			$this->destroyCachedData();
		}
	}

	/**
	 * Destroys the Site's cached data
	 *
	 * @return void
	 */
	public function destroyCachedData()
	{
		// Get the key to load from the cache
		$cacheKey = self::$cacheNamespace . '.' . $this->site->id;

		// Forget the data
		Cache::forget($cacheKey);

		// Rehydrate the cached data
		$this->loadCachedData( $this->site );
	}

    /**
     * Destroys the cached Sites in the path (useful for when
     * a Site is brand new or edited)
     *
     * @return bool
     */
	public function destroyCachedPath()
    {
        if(is_null($path = $this->path()))
            return null;

        $path->each(function($site) {
            cached_site_service($site['id'])->destroyCachedData();
        });

        return true;
    }

	/**
	 * Checks whether the cached data is current against the database
	 *
	 * @return bool
	 */
	protected function isCacheValid()
	{
		return $this->site->updated == $this->getAttributeValue('updated');
	}

	/**
	 * Get the attribute value
	 *
	 * @param  string $key
	 * @return mixed|null
	 */
	protected function getAttributeValue($key)
	{
		return array_key_exists($key, $this->attributes)
			? $this->attributes[$key]
			: null;
	}
}