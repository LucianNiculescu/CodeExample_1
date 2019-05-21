<?php
/**
 * Created by Sherif Zaki
 * Estate Singleton is responsible of creating the sessions variables (Path, Children and Estate)
 */

namespace App\Admin\Modules\Sites ;
use App\Models\AirConnect\SiteAttribute;
use App\Models\AirConnect\Site as SiteModel;

/**
 * Class EstateSingleton
 * This is the main Estate Class that fills all the sites and there are the following public functions:
 * path($siteId) which fills the allParents array except estate site which will be stored in estateID from bottom to top
 * children($siteId) which fills the allChildren array
 * estate($siteId) combination of both path and children
 * @package App\Admin\Modules\Sites
 */
class EstateSingleton
{
	protected static $instance = null; //$instance is used to create the object only once.
	private $allSites = [];		// $allSites from the DB as 'site.id' , 'site.parent', 'site_attribute.name', 'site_attribute.value'
	private $allParents = [];  	// a list of all parents of a given siteID
	private $allChildren = []; 	// a list of all children of a given siteID
	private $estateId = null;  	// the estate site of a given siteID
	private $userSite = null;	// userSite would be the session user site that came from his login

	/**
	 * EstateSingleton constructor. , protected so nobody can access it except the getInstance() public function
	 */
	protected function __construct()
	{
		// fills $allSites from the DB.
		$this->fillAllSites();
		$this->userSite = session('admin.user.site');
	}

	// singleton checks if there is an instance of the object already there if not then create a new object
	public static function getInstance($force = false)
	{
		if (!isset(static::$instance) || $force == true)
		{
			static::$instance = new EstateSingleton();
		}
		return static::$instance;
	}

	/**
	 * Returns the parents of the siteID until you find the $stopSiteId
	 * $includeEstate will decide to include the $stopSiteId or not, it is true by default
	 * estate site ID is saved in $estateId
	 * @param $startSiteId
	 * @param null $stopSiteId
	 * @param bool $includeEstate
	 * @return array of Parents from bottom to top ex: if siteID is 551 result will be [15,13,3,1,0]
	 * @internal param $siteId
	 */
	public function path($startSiteId , $stopSiteId = null, $includeEstate = true)
	{
		// Checking if $stopSiteId is null then it will take the session admin.site
		$stopSiteId = $this->checkSiteId($stopSiteId);
		$this->estateId = $stopSiteId;
		// calling getAllParents that will recursively get all Parents from $startSiteId to $stopSiteId
		$this->getAllParents($startSiteId , $stopSiteId);

		// Checking to include the estate siteId or not, default is true
		if (!$includeEstate)
		{
			// $this->estateId will be the last siteId entered to the AllParents
			$this->estateId = array_pop($this->allParents);
		}
		return $this->allParents;
	}

	/**
	 * Gets all children and grand children of the siteId
	 * @param $siteId
	 * @return array of site ids
	 */
	public function children($siteId)
	{
		// Checking if $siteId is null then it will take the session admin.site
		$siteId = $this->checkSiteId($siteId);
		return $this->getAllChildren($siteId);
	}

	/**
	 * A combination between path and children, so UP and Down
	 * @param $siteId
	 * @return array of site ids
	 */
	public function estate($siteId)
	{
		// it merges the 2 functions together and use array_unique to remove duplicates
		return array_unique(array_merge($this->path($siteId) , $this->children($this->estateId)));
	}

	/**
	 * Get ALL sites from the DB as an array
	 * joining the type of site if it is an estate (null if not)
	 * Fills the private var allSites with this array
	 *
	 * E.g. ["id": 3, "parent": 1, "name": "sitetype", "value": "estate"]
	 */
	private function fillAllSites()
	{
		// querying site table with a left join on site_attribute to get the 'site.id' , 'site.parent', 'site_attribute.name' and 'site_attribute.value'

		$this->allSites = SiteModel::leftJoin('site_attribute', function ($join) {
			$join->on('site.id', '=', 'site_attribute.ids')
				->where('site_attribute.name'   , '=', 'sitetype')
				->where('site_attribute.value'  , '=', 'estate');
		})
			->select(['site.id' , 'site.parent', 'site_attribute.name', 'site_attribute.value'])
            ->where('site.status' , '!=', 'deleted')
			->get();
	}

	/**
	 * getParent will loop into $allSites to get the Parent of a specific site id
	 *
	 * allSites looks like this {0 =>  ["id" => 1, "parent" => 0 , "name" => 'sitetype' , 'site_attribute.value' => 'estate' ]}
	 * @param $siteId
	 * @return int Parent site id
	 */
	private function getParent($siteId)
	{
		// a site will have these info inside it 'site.id' , 'site.parent', 'site_attribute.name', 'site_attribute.value'
		foreach($this->allSites as $site)
		{
			if ($site->id == $siteId)
			{
				return $site->parent;
			}
		}
		// didn't find any parent
		return null;
	}

	/**
	 * getAllParents
	 * Collects all Parents of the current site $id
	 * @param $siteId
	 * @return array Self::$allParents
	 */
	private function getAllParents($siteId , $stopSiteId)
	{
		$this->addParent($siteId);

		// stop once you reach the $stopSiteID
		if ($siteId == $stopSiteId)
		{
			return $this->allParents ;
		}
		//Checking current Parent of the site $id
		$parent = $this->getParent($siteId);

		//recursively checking parents and make sure it is not already there.
		while( $this->addParent($parent) && $parent != $stopSiteId)
		{
			//checking if there is a parent for the current parent
			$parent = $this->getParent($parent);
		}

		return $this->allParents ;
	}


	/**
	 * getChildren checks 1 level children for the given site $id
	 * @param $siteId
	 * @return array $children
	 * @internal param $id
	 */
	private function getChildren($siteId)
	{
		$children = [];
		foreach($this->allSites as $site)
		{
			if($site->parent == $siteId)
			{
				$children[] = $site->id;
			}
		}
		return $children;
	}

	/**
	 * getAllChildren recursively checks if there is children for tor the current site $id
	 * @param $siteId
	 * @return array Self::$allChildren
	 * @internal param $id
	 */
	private function getAllChildren($siteId)
	{
		// Adding the siteID to the AllChildren
		$this->addChild($siteId);

		//Checking current Children of the site $id
		$children = $this->getChildren($siteId);

		//recursively checking Children
		if(!empty($children))
		{	//checking if there is a children for the each child
			foreach ($children as $child)
			{
				$this->addChild($child);
				$this->getAllChildren($child);
			}
		}

		return $this->allChildren ;
	}

	/**
	 * checking if siteID is null or no Session variable for admin.user.site , if nothing found then return null
	 * @param $siteId
	 * @return mixed
	 */
	private function checkSiteId($siteId)
	{
		if (is_null($siteId))
		{
			$siteId = session('admin.user.site');
		}

		return $siteId;
	}

	// TODO: merge both addChild and addParent to 1 function and make it global ???
	/**
	 * Adds the siteId to the $this->allChildren[] anfter checking for duplication and if it is null or not
	 * @param $siteId
	 * @return bool
	 */
	private function addChild($siteId)
	{
		// Checking if siteId is null or not even in the Session
		$siteId = $this->checkSiteId($siteId);

		if ( !in_array($siteId, $this->allChildren) && !is_null($siteId))
		{
			$this->allChildren[] = $siteId;
			return true;
		}
		return false;
	}

	/**
	 * Adds the siteId to the $this->allParents[] anfter checking for duplication and if it is null or not
	 * @param $siteId
	 * 	@return bool
	 */
	private function addParent($siteId)
	{
		// Checking if siteId is null or not even in the Session
		$siteId = $this->checkSiteId($siteId);

		if ( !in_array($siteId, $this->allParents) && !is_null($siteId))
		{
			$this->allParents[] = $siteId;
			return true;
		}
		return false;
	}

	/**
	 * Adding current site type to the session
	 * @param $siteId
	 */
	public function addCurrentSiteTypeToSession($siteId)
	{
		$siteType = SiteAttribute::where(['ids' => $siteId, 'name' => 'siteType'])
			->first();

		// saving site type into session
		if(!empty($siteType))
			session(['admin.site.type' =>	$siteType->value ]);
		else
			session(['admin.site.type' =>	'site' ]);
	}
}