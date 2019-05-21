<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Site extends BaseModel
{
	protected $connection = 'airconnect';
	/**
	 * The table associated with the model
	 *
	 * @var string
	 */
	public $table = 'site';

	/**
	 * The attributes that are mass assignable
	 *
	 * @var array
	 */
	public $fillable = [
		'id',
		'parent',
		'name',
		'location',
		'reference',
		'contact',
		'status',
		'created',
		'updated',
		'description',
		'ip_details',
		'version'
	];

	/**
	 * The allowed site types
	 *
	 * @var array
	 */
	private static $siteTypes = ['site', 'company', 'estate'];

	/**
	 * The allowed site support types
	 *
	 * @var array
	 */
	private static $siteSupportTypes = [
		'Email only'									=>	'Email only',
		'Guest/End User 24x7'							=>	'Guest/End User 24x7',
		'Guest/End User Business Hours(9-5:30, M-F)'	=>	'Guest/End User Business Hours(9-5:30, M-F)',
		'Guest/End User Retail Hours(9-5:30, M-S)'		=>	'Guest/End User Retail Hours(9-5:30, M-S)',
		'Unknown'										=>	'Unknown',
		'Venue Only 24x7'								=>	'Venue Only 24x7',
		'Venue Only Business Hours (9-5:30, M-F)'		=>	'Venue Only Business Hours (9-5:30, M-F)',
		'Venue Only Retail Hours (9-5:30, M-S)'			=>	'Venue Only Retail Hours (9-5:30, M-S)',
	];

	/**
	 * Returns an instance of this model of the currently logged in site. Defaults to not loading
	 * the model from the database, instead returning an instance with the ID populated allowing
	 * access to the relationships instead of hitting the database. Specify $loadFullModel as true
	 * to retrieve the model from the database
	 *
	 * @param  bool $loadFullModel
	 * @return Site
	 */
	public static function loggedIn($loadFullModel = false)
	{
		return $loadFullModel
					? self::find(session('admin.site.loggedin'))
					: new self(['id' => session('admin.site.loggedin')]);
	}

	/**
	 * Gets site type with the key as the type and the value is the translated value
	 * @return array
	 */
	public static function getSiteTypes()
	{
		$result = [];

		// Looping into sitetypes and translate the values
		foreach (self::$siteTypes as $siteType)
		{
			$result[$siteType] = trans('admin.' . $siteType);
		}

		return $result;
	}


	public static function getSiteSupportTypes()
	{
		return self::$siteSupportTypes;
	}

	/**
	 * Get the Gateways for the Site
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function gateways()
	{
		return $this->hasMany( '\App\Models\AirConnect\Gateway', 'site');
	}

	/**
	 * Do a join on the hardware table (in airhealth), foreign key site
	 * Sites can have many hardware
	 * @return mixed
	 */
	public function hardware()
	{
		return $this->hasMany( '\App\Models\AirHealth\Hardware', 'site' );
	}

	/**
	 * Do a join on the admin table, foreign key is site.
	 * site can have many admin
	 * @return mixed
	 */
	public function admins()
	{
		return $this->hasMany( '\App\Models\AirConnect\Admin', 'site' );
	}

	/**
	 * Do a join on the blocked table, foreign key is site.
	 * site can have many blocked
	 * @return mixed
	 */
	public function blocked()
	{
		return $this->hasMany( '\App\Models\AirConnect\Blocked', 'site' );
	}

	/**
	 * Do a join on the browsers table, foreign key is site.
	 * site can have many browsers
	 * @return mixed
	 */
	public function browsers()
	{
		return $this->hasMany( '\App\Models\AirConnect\Browser', 'site' );
	}

	/**
	 * Do a join on the bypassed table, foreign key is siteid.
	 * site can have many bypassed
	 * @return mixed
	 */
	public function bypassed()
	{
		return $this->hasMany( '\App\Models\AirConnect\Bypassed', 'siteid' );
	}

	/**
	 * Do a join on the messages table, foreign key is site.
	 * site can have many messages
	 * @return mixed
	 */
	public function messages()
	{
		return $this->hasMany( '\App\Models\AirConnect\Message', 'site' );
	}

	/**
	 * Get the packages for the site
	 *
	 * @return mixed
	 */
	public function packages()
	{
		return $this->hasMany( '\App\Models\AirConnect\Package', 'site' );
	}

	/**
	 * Do a join on the portal table, foreign key is site.
	 * site can have many portal
	 * @return mixed
	 */
	public function portals()
	{
		return $this->hasMany( '\App\Models\AirConnect\Portal', 'site' );
	}

	/**
	 * Do a join on the reminder table, foreign key is site.
	 * site can have many reminder
	 * @return mixed
	 */
	public function reminders()
	{
		return $this->hasMany( '\App\Models\AirConnect\Reminder', 'site' );
	}

	/**
	 * Get the parent which own the Site
	 *
	 * @return mixed
	 */
	public function parent()
	{
		return $this->hasOne( '\App\Models\AirConnect\Site', 'id', 'parent' );
	}

	/**
	 * Get the children belonging to the Site
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function children()
	{
		return $this->hasMany( '\App\Models\AirConnect\Site', 'parent');
	}

	/**
	 * Do a join on the site_attribute table, foreign key is ids.
	 * site can have many site_attribute
	 * @return mixed
	 */
	public function attributes()
	{
		return $this->hasMany( '\App\Models\AirConnect\SiteAttribute', 'ids' );
	}

	/**
	 * Do a join on the template table, foreign key is site.
	 * site can have many template
	 * @return mixed
	 */
	public function templates()
	{
		return $this->hasMany( '\App\Models\AirConnect\Template', 'site' );
	}

	/**
	 * Do a join on the transaction table, foreign key is site.
	 * site can have many transaction
	 * @return mixed
	 */
	public function transactions()
	{
		return $this->hasMany( '\App\Models\AirConnect\Transaction', 'site' );
	}

	/**
	 * Do a join on the user table, foreign key is site.
	 * site can have many user
	 * @return mixed
	 */
	public function users()
	{
		return $this->hasMany( '\App\Models\AirConnect\User', 'site' );
	}

	/**
	 * Do a join on the user_connection table, foreign key is site.
	 * site can have many user_connection
	 * @return mixed
	 */
	public function userConnections()
	{
		return $this->hasMany( '\App\Models\AirConnect\UserConnection', 'site' );
	}

	/**
	 * Do a join on the voucher table, foreign key is site.
	 * site can have many voucher
	 * @return mixed
	 */
	public function vouchers()
	{
		return $this->hasMany( '\App\Models\AirConnect\Voucher', 'site' );
	}

	/**
	 * Do a join on the forms table, foreign key is site.
	 * site can have many voucher
	 * @return mixed
	 */
	public function forms()
	{
		return $this->hasMany( '\App\Models\AirConnect\Form' );
	}

	/**
	 * Do a join on the walledgarden table, foreign key is site.
	 * site can have many walledgarden
	 * @return mixed
	 */
	public function walledgarden()
	{
		return $this->hasMany( '\App\Models\AirConnect\Walledgarden', 'site' );
	}

	/**
	 * Do a join on the whitelist table, foreign key is site.
	 * site can have many whitelist
	 * @return mixed
	 */
	public function whitelist()
	{
		return $this->hasMany( '\App\Models\AirConnect\Whitelist', 'site' );
	}

	/**
	 * Do a join on the whitelist table, foreign key is site.
	 * site can have many whitelist
	 * @return mixed
	 */
	public function guestWhitelist()
	{
		return $this->hasMany( '\App\Models\AirConnect\GuestWhitelist');
	}

	/**
	 * Do a join on the location table.
	 * site can have many locations
	 * @return mixed
	 */
	public function locations()
	{
		return $this->hasMany( '\App\Models\AirConnect\Location' );
	}

	public function roles()
	{
		return $this->hasMany( '\App\Models\AirConnect\Role' );
	}

	/**
	 * Do a join on the content table.
	 * site can have many content
	 * @return mixed
	 */
	public function content()
	{
		return $this->hasMany( '\App\Models\AirConnect\Content', 'site' );
	}

	/**
	 * Scope a query to return the free packages for the site (includes query limits
	 * to take into account package type names in V1 system)
	 *
	 * @return mixed
	 */
	public function scopeFreePackages()
	{
		return $this->packages()->where(function ($query) {
					$query->where(['type' => 'email', 'status' => 'active'])
						->orWhere(['type' => 'free', 'status' => 'active'])
						->orWhere(['type' => 'paid', 'cost' => 0, 'status' => 'active']);
				});
	}

	/**
	 * Returns the cached data for the Site
	 *
	 * @return \App\Admin\Modules\Sites\CachedSite
	 */
	public function cachedData()
	{
		return cached_site_service($this);
	}
}