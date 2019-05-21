<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;
use App\Traits\TranslatableAttributes;

/**
 * Class Package
 *
 * A Guest purchases a package on a site which defines the speed and login credential format
 * for accessing the site's gateway
 *
 * @package App\Models\AirConnect
 * @link https://github.com/airangel/myairangel-v3/wiki/Packages
 */
class Package extends BaseModel
{
	use TranslatableAttributes;

	/**
	 * The connection name for the model
	 *
	 * @var string
	 */
	public $connection = 'airconnect';

	/**
	 * The table associated with the model
	 *
	 * @var string
	 */
	protected $table = 'package';

	/**
	 * The attributes that are mass assignable
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'description',
		'type',
		'cost',
		'status',
		'site',
	];

	/**
	 * The attributes which represent timestamps
	 *
	 * @var array
	 */
	public $timestamps = ['created_at'];

	/**
	 * Whether to translate attributes on __get()
	 * @var bool
	 */
	protected $translateOnGet = false;

	/**
	 * The attributes which have translations
	 *
	 * @var array
	 */
	protected $translatable = [
		'type',
	];

	/**
	 * The allowed package types
	 *
	 * @var array
	 */
	public static $types = [
		'email',
		'voucher',
		'facebook',
		'twitter',
		'gha',
		'google',
		'linkedin',
		'live',
//		'pms', // Will be added on the fly after doing a check on site attribute with type pms
		'quick_login',
		'voyat',
		'whitelist'
	];

	/**
	 * The allowed package statuses
	 *
	 * @var array
	 */
	public static $statuses = [
		'active',
		'inactive',
		'delete'
	];

	/**
	 * Package types of which a site can have many
	 * (all others should be one to each site)
	 *
	 * @var array
	 */
	public static $typeMultiples = [
		'email',
		'voucher',
		'pms',
		'whitelist'
	];

	/**
	 * The default type for a package
	 *
	 * @var string
	 */
	public static $defaultType = 'email';

	/**
	 * Do a join on the package_attribute table, foreign key is ids.
	 * package can have many package_attribute.
	 *
	 * @return mixed
	 */
	public function attributes()
	{
		return $this->hasMany( '\App\Models\AirConnect\PackageAttribute', 'ids' );
	}

	/**
	 * Alternative name for package attributes for correct Eloquent functionality
	 *
	 * @return mixed
	 */
	public function packageAttributes()
	{
		return $this->attributes();
	}

	/**
	 * Do a join on the voucher table, foreign key is package.
	 * package can have many voucher
	 * @return mixed
	 */
	public function vouchers()
	{
		return $this->hasMany( '\App\Models\AirConnect\Voucher', 'package' );
	}

	/**
	 * Get the site that owns the package
	 *
	 * @return mixed
	 */
	public function site()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'site' );
	}

	/**
	 * Alternative relationship name for $this->site() due to the foreign key
	 * field name being `site`
	 *
	 * @return mixed
	 */
	public function parentSite()
	{
		return $this->site();
	}

	/**
	 * Get the transactions for the package
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function transactions()
	{
		return $this->hasMany( '\App\Models\AirConnect\Transaction', 'package_id', 'id');
	}

	/**
	 * Scope a query to get the currently active transactions for the package
	 *
	 * @return mixed
	 */
	public function scopeActiveTransactions()
	{
		return $this->transactions()->where([
				[ 'status', '=', 'Completed'],
				[ 'expires', '>',  \Carbon\Carbon::now()]
			]
		);
	}

	/**
	 * Scope a query to accept a list of package attribute names and construct
	 * the join to include in the query (for efficiency as opposed to Eloquent's joins)
	 *
	 * @param $query
	 * @param array $attributes
	 * @return mixed
	 */
	public function scopeIncludePackageAttributes($query, array $attributes)
	{
		// Add join and select statement for each attribute provided
		foreach($attributes as $attribute)
		{
			// Create a unique name for the join
			$joinName = uniqid($attribute);

			// Add the join
			$query->leftJoin('package_attribute as ' . $joinName, function($join) use ($attribute, $joinName) {
				$join->on('package.id', '=', $joinName . '.ids')
					->where($joinName . '.name', '=', $attribute);
			});

			// Add select
			$query->addSelect($joinName . '.value as ' . $attribute);
		}

		return $query;
	}

	/**
	 * Scope a query to get a distinct list of package types in use for the logged in site
	 *
	 * @TODO   Some of this logic belongs in Site model
	 * @param  $query
	 * @return mixed
	 */
	public function scopeTypesInUse($query)
	{
		return $query->where([
						'site'		=> session('admin.site.loggedin'),
						'status'	=> 'active'
					])
					->addSelect('type')
					->distinct();
	}

	/**
	 * Returns the value of the PackageAttribute model of the name $attributeName
	 * associated with this model. Optionally accepts default value to return if
	 * attribute isn't found, and optionally specify the type
	 *
	 * @param  string $attributeName
	 * @param  mixed  $defaultValue
	 * @return mixed
	 */
	public function getPackageAttributeValue($attributeName, $defaultValue = 0)
	{
		// Limit the package attributes by the name we're trying to find
		$attribute = $this->packageAttributes->where('name', $attributeName);

		// If the Collection is empty, return a default value
		if($attribute->isEmpty())
			return $defaultValue;

		// Attribute found, grab the PackageAttribute record from the collection
		$attribute = $attribute->first();

		return $attribute->value;
	}

	/**
	 * Get the localised strings from the package's attributes as an array
	 *
	 * @return array
	 */
	public function getPackageLocalisedStrings()
	{
		// Build multidimensional array
		$localisedStrings = [];

		foreach($this->packageAttributes as $attr)
		{
			// Only grab title and placeholder attributes
			if(in_array($attr->name, ['title', 'placeholder']))
				$localisedStrings[$attr->name][$attr->type] = $attr->value;
		}

		return $localisedStrings;
	}

	/**
	 * Get the type attribute, which if 'paid' or 'free', is returned as 'email'
	 *
	 * @return string
	 */
	public function getTypeAttribute()
	{
		return ($this->attributes['type'] == 'paid' or $this->attributes['type'] == 'free')
			? 'email'
			: $this->attributes['type'];
	}


	/**
	 * Do a join on the whitelist table, foreign key is site.
	 * site can have many whitelist
	 * @return mixed
	 */
	public function guestwhitelist()
	{
		return $this->hasMany( '\App\Models\AirConnect\GuestWhitelist' );
	}

}