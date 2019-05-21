<?php

namespace App\Admin\Modules\Sites\Services;

use DB;
use App\Models\AirConnect\Site;
use Illuminate\Support\Collection;

/**
 * Class responsible for resolving a Site's inherited relations
 */
class InheritanceService
{
	/**
	 * Return a collection of Sites which are the parents of the given Site
	 * up to the first encountered estate estate
	 *
	 * @param  Site $site
	 * @return Collection  - Ordered from child to parents
	 */
	public static function getPath(Site $site) : Collection
	{
		// Build the path from one query
		$inheritanceView = self::getInheritanceView($site, true);

		// Return a linear unique list representing the path
		return self::transformInheritanceViewToUniqueCollection( $inheritanceView , true );
	}

	/**
	 * Builds and returns a collection of a Site's children, with the option to get children
	 * down all branches of the family tree
	 *
	 * @param  Site $site
	 * @param  bool $recursive
	 * @return Collection
	 */
	public static function getChildren(Site $site, $recursive = false) : Collection
	{
		// Return the immediate children if not recursive
		if(!$recursive)
			return $site->children()->get();

		// Get all Sites which are children of the current Site
		$inheritanceView = self::getInheritanceView($site);

		return self::transformInheritanceViewToUniqueCollection( $inheritanceView );
	}

	/**
	 * Returns a recordset of all Sites which are children of the given Site (recursively)
	 * The function can be reversed to give the path of the given Site
	 *
	 * Each row is one site, it's ID, and it's parent ID, which is a child of the given Site.
	 * The fields of the recordset are dynamically built in a pseudo-recursive fashion:
	 *
	 * |1_site_id | 1_site_parent | 1_site_name | 2_site_id | 2_site_parent | 2_site_name | ... |
	 * 2_site_parent will be joined to 1_site_id, 3_site_parent will be joined to 2_site_id etc
	 *
	 * The number of self-joins is determined by the cached longest Site path of the entire
	 * application
	 *
	 * @param  Site         $site
	 * @param  bool         $reverse
     * @throws \Exception   When recursion level goes above 20
	 * @return array
	 */
	public static function getInheritanceView(Site $site, $reverse = false)
	{
		// Get the longest path of any cached Site to use as the number of joins
		$service = app()->make(CachedSiteService::class);
		$recursionLevel = $service->largestPathLength;

		if($recursionLevel > 20)
		    throw new \Exception('Recursion level limit hit at 20');

		// Begin building the query
		$query = DB::table('site as 1_site')
					->select(['1_site.id as 1_site_id', '1_site.parent as 1_site_parent', '1_site.name as 1_site_name', '1_site.status as 1_site_status', '1_site_attribute.value as 1_site_type']);

		// Add where statement based on $reverse - if true, we will limit to the Site, not it's parent
		$reverse ? $query->where('1_site.id',$site->id) : $query->where('1_site.parent',$site->id);

		// Add the join to get the Site type from it's attributes
		$query->lefTJoin('site_attribute as 1_site_attribute', function($join) {
			$join->on('1_site.id', '=', '1_site_attribute.ids')
				->where('1_site_attribute.name', '=', 'sitetype');
		});

		// Self join over again for the recursion level
		for($i=2;$i<($recursionLevel + 2);$i++)
		{
			$alias = $i.'_site';
			$previousAlias = ($i-1).'_site';

			// Join based on $reverse
			$reverse ? $query->leftJoin('site as '.$alias, $alias.'.id', '=', $previousAlias.'.parent')
						: $query->leftJoin('site as '.$alias, $alias.'.parent', '=', $previousAlias.'.id');

			$query->lefTJoin('site_attribute as ' . $alias . '_attribute', function($join) use ($alias) {
				$join->on($alias.'.id', '=', $alias . '_attribute.ids')
					->where($alias . '_attribute.name', '=', 'sitetype');
			});
			$query->addSelect([
				$alias.'.id as '. $alias.'_id',
				$alias.'.parent as '. $alias.'_parent',
				$alias.'.name as '. $alias.'_name',
                $alias.'.status as '. $alias.'_status',
				$alias.'_attribute.value as ' . $alias . '_type'
			]);
		}

		// Build a collection from the returned data
		$data = collect($query->get());

		// Check whether the furthest right of the table has data, implying the number of joins needs to be increased
		// as there may be deeper levels. The cached largest path is increased by 1 and this function called again
		// which is repeated until we know there are no deeper levels
		$furthestRightKey = $recursionLevel + 1;
		$furthestRightJoinedData = $data
			// Create a collection of just the ID's
			->map(function($record) use ($furthestRightKey) {
				return [ 'id' => $record->{$furthestRightKey.'_site_id'} ];
			}) // Filter out null records
			->reject(function($record, $key) {
				return is_null($record['id']);
			}) // Ensure the list is unique
			->unique('id');

		// If the furthest right of the table has no data, our largest path is grabbing all the required data
		if($furthestRightJoinedData->isEmpty())
			return $data;

		// Increment the largest path and rerun this method
		cached_site_service()->largestPathLength($furthestRightKey + 1);

		return self::getInheritanceView($site, $reverse);

	}

	/**
	 * @param  Collection $collection
     * @param  boolean    $exceptionOnDuplicate Throw an exeption if the resulting collection contains duplicates
     * @throws \Exception When $exceptionOnDuplicate is true and duplicate Site IDs are found
	 * @return Collection
	 */
	protected static function transformInheritanceViewToUniqueCollection(Collection $collection, $exceptionOnDuplicate = false)
	{
		// Get the recursion level
		$recursionLevel = cached_site_service()->largestPathLength;
		$result = collect([]);

		// Loop through recordset to build a list of children
		for($i=1;$i<($recursionLevel + 2);$i++)
		{
			$sites = $collection
				// Pull out the list of children for the recursion level
				->map(function($record) use ($i) {
					return [
						'id' 		=> $record->{$i.'_site_id'},
						'parent' 	=> $record->{$i.'_site_parent'},
						'name' 		=> $record->{$i.'_site_name'},
                        'status'    => $record->{$i.'_site_status'},
						'type'		=> $record->{$i.'_site_type'}
					];
				})
				// Filter out null records
				->reject(function($record, $key) {
					return is_null($record['id']);
				});

            // Ensure the list is unique
            $uniqueSites = $sites->unique('id');

            if($exceptionOnDuplicate && $uniqueSites->count() !== $sites->count())
                throw new \Exception('Duplicate Sites found in Site Path');

			// Add to our list of Sites
			if(!$uniqueSites->isEmpty())
				$result = $result->merge($uniqueSites);
		}

		return $result;
	}
}