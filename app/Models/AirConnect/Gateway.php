<?php
namespace App\Models\AirConnect;

use App\Admin\Search\Searchable;
use App\Models\BaseModel;

/**
 * Class Gateway
 *
 * @package App\Models\AirConnect
 */
class Gateway extends BaseModel
{

	use Searchable; // Can be indexed to elasticsearch
	protected $connection = 'airconnect';
	/**
	 * The table associated with the model
	 *
	 * @var string
	 */
	public $table = 'gateway';

	/**
	 * The attributes that are mass assignable
	 *
	 * @var array
	 */
    public $fillable = [
    	'site',
		'name',
		'type',
		'location',
		'username',
		'password',
		'status',
		'ip',
		'mac'
	];

	/**
	 * The allowed Gateway types
	 *
	 * @var array
	 */
	private static $gatewayTypes = [
		'MIKROTIK'      =>  'Candengo',
		'CISCO'         =>  'Cisco',
		'COLUBRIS'      =>  'Colubris',
		'COOVA'         =>  'Coova',
		'XIRRUS'        =>  'Xirrus',
		'RUCKUS'        =>  'Ruckus',
		'NOMADIX'       =>  'Nomadix',
		'CISCOMERAKI'   =>  'Cisco Meraki',
	];

	/**
	 * Gets gateway types as an array
	 * @return array
	 */
	public static function getGatewayTypes()
	{
		return self::$gatewayTypes;
	}

	/**
	 * Get the first active Gateway by Mac Address
	 * @param $mac
	 * @return mixed
	 */
	public static function getFirstActiveGatewayByMac($mac)
	{
		return self::where( ['mac' => $mac, 'status' => 'active'] )->first();
	}

	/**
	 * Get the first active Gateway by Site
	 *
	 * @param $site
	 * @return mixed
	 */
	public static function getFirstActiveGatewayBySite($site)
	{
		return self::where( ['site' => $site, 'status' => 'active'] )
			->select('mac','ip', 'location')
			->first();
	}

	/**
	 * Get all active Gateways by Site
	 *
	 * @param $site
	 * @return mixed
	 */
	public static function getAllGatewayBySite($site, $keyBy = '')
	{
		$gateways = self::where( ['site' => $site, 'status' => 'active'] )->orderBy('id')->get();

		if(!empty($keyBy))
			$gateways = $gateways->keyBy($keyBy);

		return $gateways;
	}

	/**
	 * Do a join on the gateway_attribute table, foreign key is ids.
	 * user can have many gateway_attribute
	 * @return mixed
	 */
	public function attributes()
	{
		return $this->hasMany( '\App\Models\AirConnect\GatewayAttribute', 'ids' );
	}

	/**
	 * Do a join on the AirHealth.hardware table, foreign key is mac
	 * @return mixed
	 */
	public function hardware()
	{
		return $this->hasOne( '\App\Models\AirHealth\Hardware', 'mac', 'mac' );
	}

	/**
	 * user belongs to site
	 * @return mixed
	 */
	public function site()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'site');
	}
	/**
	 * Returns the name of the class responsible for managing radius attributes for the
	 * type of this Gateway
	 *
	 * @return string
	 */
	public function resolveTypeClassName()
	{
		return '\App\Portal\Radius\GatewayType\\' .ucwords( strtolower( $this->type ));
	}

	public function scopeTypes($query)
	{
		return $query->select('type')->distinct();
	}

	public static function getGatewayFromMac($mac)
	{
		return self::where('mac', $mac)->first();
	}
}