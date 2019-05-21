<?php
namespace App\Admin\Modules\Packages;

use Illuminate\Http\Request;
use Event;

use App\Helpers\DateTime;
use App\Models\AirConnect\Attribute;
use App\Models\AirConnect\Package;
use App\Models\AirConnect\PackageAttribute;
use App\Admin\Modules\Packages\Events\PackageUpdated;
use App\Admin\Helpers\HumanReadable;
use App\Models\AirConnect\SiteAttribute as SiteAttributeModel;

class Logic
{
	/**
	 * Validates the package is part of the logged in site
	 *
	 * @param Package $package
	 */
	public static function validateSiteRelationship(Package $package)
	{
		if(session('admin.site.loggedin') != $package->site)
			abort('401', trans('error.not-authorized'));
	}

	/**
	 * Creates or updates a Package and the record's attributes based on a validated
	 * form request
	 *
	 * @param  \Illuminate\Http\Request 			$request
	 * @param  null|\App\Models\AirConnect\Package $package
	 * @return \App\Models\AirConnect\Package
	 */
	public static function setPackageFromRequest(Request $request, $package = null)
	{
		$updating = !is_null($package);

		// Build and manipulate request data
		$data = $request->only([
			'name',
			'description',
			'type',
			'cost'
		]);
		$data['cost'] = is_numeric($data['cost']) ? $data['cost'] : 0;

		// Updating a package
		if($updating)
		{
			// Update legacy values, otherwise unset type
			if(in_array($data['type'], ['free', 'paid']))
			{
				$data['type'] = 'email';
			} else {
				unset($data['type']);
			}

			// If the user has opted to replace the package instead of updating,
			// clone the package and soft delete the current one
			if($request->update_transactions === 'replace')
			{
				// Replicate the package and save, soft delete the old package
				$oldPackage = $package;
				$package 	= $oldPackage->replicate();

				$oldPackage->update(['status' => 'delete']);
				$package->save();
			}

			// Update the package and remove existing attributes ready to replace
			$package->update($data);
			$package->attributes()->delete();

		} else {
			// Creating a package
			$package = Package::create([
					'site' 		=> session('admin.site.loggedin'),
					'status'	=> 'active'
			] + $data);
		}

		// Build array of PackageAttribute models from request to store against the package
		$attributeData = [];

		// Build language title and placeholder attributes from the request
		$attributeData += self::getLanguageTitlesAsAttributes($package, $request->{'lang-title'});
		$attributeData += self::getLanguagePlaceholdersAsAttributes($package, $request->{'lang-placeholder'});

		// Build attributes from array values in request
		$attributesCombined = array_combine($request->attribute_name, $request->attribute_value);
		$attributeData += self::getPackageAttributesAsAttributes($package, $attributesCombined);

		// Build other attributes coming from the request (with optional mutator function
		// to alter the value before creating the attribute record)
		$attributeData += self::getAttributesFromRequestByConfig($package, $request);

		// Store attributes against Package
		$package->attributes()->saveMany($attributeData);

		// Fire the PackageUpdated event if we are updating, which will in turn
		// update transactions if the user has specified to
		$shouldUpdateTransaction = $request->update_transactions === 'yes';
		if($updating)
			Event::fire(new PackageUpdated($package, $shouldUpdateTransaction, $request->gateway_id));

		return $package;
	}

	/**
	 * Takes an array of form field values for the lang-title array field
	 * on the package form blade and creates an array of Attribute models
	 * to save against the package
	 *
	 * @param  Package $package
	 * @param  array 		$titles
	 * @return array of PackageAttribute models
	 */
	private static function getLanguageTitlesAsAttributes(Package $package, $titles)
	{
		$attributes = [];

		foreach($titles as $type => $value)
			if(!empty($value))
				$attributes['langt.'.$type.$value] = new PackageAttribute([
					'ids' 		=> $package->id,
					'name' 		=> 'title',
					'type' 		=> $type,
					'value' 	=> $value,
					'status' 	=> 'active'
				]);

		return $attributes;
	}

	/**
	 * Takes an array of form field values for the lang-placeholder array field
	 * on the package form blade and creates an array of Attribute models
	 * to save against the package
	 *
	 * @param  Package $package
	 * @param  array	    $placeholders
	 * @return array of PackageAttribute models
	 */
	private static function getLanguagePlaceholdersAsAttributes(Package $package, $placeholders)
	{
		$attributes = [];

		foreach($placeholders as $type => $value)
			if(!empty($value))
				$attributes['langp.'.$type.$value] = new PackageAttribute([
					'ids' 		=> $package->id,
					'name' 		=> 'placeholder',
					'type' 		=> $type,
					'value' 	=> $value,
					'status' 	=> 'active'
				]);

		return $attributes;
	}

	/**
	 * Takes an array of form field values for the attribute_name and attribute_value array
	 * fields on the package form blade (expects these to be array_combined) and creates an
	 * array of Attribute models to save against the package
	 *
	 * @param  Package $package
	 * @param  array		$requestAttributes
	 * @return array of PackageAttribute models
	 */
	private static function getPackageAttributesAsAttributes(Package $package, $requestAttributes)
	{
		$attributes = [];

		// Get a list of package attributes and those which the user can't select for validation
		$packageAttributes = Attribute::list('package')->get()
										->keyBy('attribute');
		$allowedAttributes = $packageAttributes->forget(SetupViewData::$hideFromAttributes)->toArray();
		$packageAttributes = $packageAttributes->toArray();

		// Attribute names and values
		foreach($requestAttributes as $attrName => $value)
		{
			// Use the value from the request or see if there's a default value to set
			$value = empty($value)
						? Attribute::getDefaultValue('package', $attrName)
						: $value;

			// Proceed if attribute is allowed to be created and has a value to set
			if(array_key_exists($attrName, $allowedAttributes) && $value !== false)
			{
				$type = $packageAttributes[$attrName]['insert-table'];
				$type = empty($type) ? 'radreply' : $type;

				$attributes[$attrName.$type] = new PackageAttribute([
					'ids' 		=> $package->id,
					'name' 		=> $attrName,
					'type' 		=> $type,
					'value' 	=> $value,
					'status' 	=> 'active'
				]);
			}
		}

		return $attributes;
	}

	/**
	 * Returns a list of field names expected in the request to store/update as Package attributes,
	 * as well as the intended attribute type, default value (optional), and value mutator
	 * (optional)
	 *
	 * @return array
	 */
	private static function requestValuesConfig()
	{
		return [
			[ 'name' => 'download', 'attr_name' => 'downstream', 'type' => 'radreply', 'default' => 0, 'mutator' => function($val) { return $val * 1024; } ],
			[ 'name' => 'upload', 'attr_name' => 'upstream', 'type' => 'radreply', 'default' => 0, 'mutator' => function($val) { return $val * 1024; } ],
			[ 'name' => 'facebook_like', 'type' => 'package' ],
			[ 'name' => 'facebook_share', 'type' => 'package' ],
			[ 'name' => 'facebook_message', 'type' => 'package' ],
			[ 'name' => 'redirect_uri', 'type' => 'package' ],
			[ 'name' => 'tiered-bandwidth', 'type' => 'package' ],
			[ 'name' => 'duration', 'type' => 'radreply', 'default' => 30, 'mutator' => function() { return self::formatPackageDuration(); }],
			[ 'name' => 'duration_type', 'type' => 'package', 'default' => 'days' ],
			[ 'name' => 'gold-download', 'attr_name' => 'gold-downstream', 'type' => 'radreply', 'default' => 0, 'mutator' => function($val) { return $val * 1024; } ],
			[ 'name' => 'gold-upload', 'attr_name' => 'gold-upstream', 'type' => 'radreply', 'default' => 0, 'mutator' => function($val) { return $val * 1024; } ],
			[ 'name' => 'platinum-download', 'attr_name' => 'platinum-downstream', 'type' => 'radreply', 'default' => 0, 'mutator' => function($val) { return $val * 1024; } ],
			[ 'name' => 'platinum-upload', 'attr_name' => 'platinum-upstream', 'type' => 'radreply', 'default' => 0, 'mutator' => function($val) { return $val * 1024; } ],
			[ 'name' => 'black-download', 'attr_name' => 'black-downstream', 'type' => 'radreply', 'default' => 0, 'mutator' => function($val) { return $val * 1024; } ],
			[ 'name' => 'black-upload', 'attr_name' => 'black-upstream', 'type' => 'radreply', 'default' => 0, 'mutator' => function($val) { return $val * 1024; } ],
		];
	}

	/**
	 * Returns a the number of seconds based on given days or hours
	 *
	 * @return string
	 */
	private static function formatPackageDuration() {
		//Get the request
		$request = \Request::all();
		//Check if the default duration has been set
		if(empty($request['duration']))
			$request['duration'] = 30;
		//Check if the user has set days or hours
		if(!empty($request['duration_type']) && $request['duration_type'] == 'hours')
			return DateTime::hours2seconds($request['duration']);
		else
			return DateTime::days2seconds($request['duration']);
	}

	/**
	 * Returns a list of field names expected in the request to store/update as Site attributes
	 * @return array
	 */
	private static function requestSiteAttributes()
	{
		return [
			[ 'name' => 'enrollment_code',				'type' => 'gha' ],
			[ 'name' => 'brand', 						'type' => 'gha' ],
			[ 'name' => 'address-flag',					'type' => 'gha' ],
			[ 'name' => 'phone-flag',					'type' => 'gha' ],
			[ 'name' => 'email-is-username-flag',		'type' => 'gha' ],
			[ 'name' => 'no-repeat-password-flag',		'type' => 'gha' ],
			[ 'name' => 'packages-gha-email', 			'type' => 'gha' ],
			[ 'name' => 'key', 							'type' => 'voyat' ],
		];
	}

	/**
	 * Takes an internal configuration array (self::requestValuesConfig() of fields expected
	 * in the request to store/update Package attributes and creates an array of attributes to
	 * store against the Package
	 *
	 * @param Package $package
	 * @param Request	   $request
	 * @return array
	 */
	private static function getAttributesFromRequestByConfig(Package $package, Request $request)
	{
		$expectedFields = self::requestValuesConfig();
		$attributes = [];

		foreach($expectedFields as $config)
		{
			// Get the field value from the request
			$fieldValue = $request->{$config['name']};

			// Create attribute if we have a value or there is a default set in the config
			if(!empty($fieldValue) or isset($config['default']))
			{
				// Set initial value
				$value = empty($fieldValue) ? $config['default'] : $fieldValue;

				// Run any configuration callback
				if(isset($config['mutator']))
					$value = $config['mutator']($value);

				$attributes[$config['name'].$config['type']] = new PackageAttribute([
					'ids' 		=> $package->id,
					'name' 		=> isset($config['attr_name']) ? $config['attr_name'] : $config['name'],
					'type' 		=> $config['type'],
					'value' 	=> $value,
					'status' 	=> 'active'
				]);
			}
		}

		return $attributes;
	}


	/**
	 * Takes an internal configuration array (self::requestSiteAttributes() of fields expected
	 * in the request to store/update Site attributes and creates an array of attributes to
	 * store against the Loggedin site
	 *
	 * @param Request	   $request
	 * @return array
	 */
	public static function setSiteAttributesFromRequest(Request $request)
	{
		$expectedFields = self::requestSiteAttributes();
		$attributes = [];

		foreach($expectedFields as $config)
		{
			// Get the field value from the request
			$value = $request->{$config['name']};

			// Create attribute if we have a value or there is a default set in the config
			if(!empty($value))
			{
				$attributes[] = [
					'ids' 		=> session('admin.site.loggedin'),
					'name' 		=> $config['name'],
					'type' 		=> $config['type'],
					'value' 	=> $value,
					'status' 	=> 'active'
				];
			}
		}

		return $attributes;
	}

	/**
	 * Gets the data from the site attributes
	 * The returned array will have a key formed from concatenating attribute type and name
	 *
	 * @param array $field
	 * @return array
	 * @internal param $fields - array containing names of site attributes to be returned
	 */
	public static function getSiteAttributesData( $field=[])
	{
		$siteAttributes = self::siteAttributesData($field)->get();
		$siteAttributesData = [];

		foreach ($siteAttributes as $siteAttribute)
			$siteAttributesData[$siteAttribute->type . '-' . $siteAttribute->name] = $siteAttribute->value;

		return $siteAttributesData;
	}

	/**
	 * Builds a query for the site attributes
	 *
	 * @param $fields - array containing names of site attributes to be queried
	 * @return array
	 */
	public static function siteAttributesData( $fields = [])
	{
		// Have any attributes been specified
		if(empty($fields))
		 	// Use the attribute names from self::requestSiteAttributes()
			$expectedFields = self::requestSiteAttributes();
		else
			//Use the attributes specified in the parameter
			$expectedFields = $fields;

		$attributes = SiteAttributeModel::where('ids', session('admin.site.loggedin'));

		$attributes = $attributes->where(function($query) use($expectedFields){
			// Looping into siteAttributes
			foreach($expectedFields as $values)
			{
				// Getting all saved data
				$query = $query->orWhere(function($q) use ($values) {

					foreach ($values as $key=>$value) {

						$q = $q->where($key, $value);
					}
					return $q;
				});

			}
		});
		return $attributes;
	}

	/**
	 * Returns the package attributes values in a human readable form
	 * @param $packageId
	 * @param bool $join
	 * @return mixed
	 */
	public static function getHumanReadableByPackage($packageId, $join) {
		$package = Package::where('id',$packageId)->with(explode(',',$join))->first();
		if(!empty($package->attributes))
			foreach($package->attributes as $attr)
				$attr->value = HumanReadable::readable($attr->name, $attr->value);

		return $package;
	}


}