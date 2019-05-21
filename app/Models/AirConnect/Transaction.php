<?php
namespace App\Models\AirConnect;

//use App\Admin\Search\Searchable;
use App\Models\BaseModel;

class Transaction extends BaseModel
{
	// Can be indexed to elasticsearch (not now)
//	use Searchable;
	protected $connection = 'airconnect';
	public $table = 'transaction';
	public $timestamps = false;
	protected $fillable = array('guid', 'name', 'package_id', 'user', 'site', 'type', 'status', 'amount', 'payment_type', 'expires');

	const EMAIL_TYPES = [
		'email',
		'paid',
		'free'
	];

	/**
	 * transaction belongs to site
	 * @return mixed
	 */
	public function site()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'site' );
	}

	/**
	 * Do a join on the voucher table, foreign key is guid.
	 * transaction can have many voucher
	 * @return mixed
	 */
	public function vouchers()
	{
		return $this->hasMany( '\App\Models\AirConnect\Voucher', 'guid' );
	}

	/**
	 * Do a join on the attributes table, foreign key is ids.
	 * transaction can have many attributes
	 * @return mixed
	 */
	public function attributes()
	{
		return $this->hasMany( '\App\Models\AirConnect\TransactionAttribute', 'ids' );
	}

	/**
	 * transaction belongs to user
	 * @return mixed
	 */
	public function guest()
	{
		return $this->belongsTo( '\App\Models\AirConnect\User', 'user', 'id' );
	}

	/**
	 * transaction belongs to package
	 * @return mixed
	 */
	public function package()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Package', 'package_id', 'id' );
	}

	/**
	 * Do a join on the radacct table, foreign key is username.
	 * transaction can have many radacct
	 * @return mixed
	 */
	public function radacct()
	{
		return $this->hasMany( '\App\Models\Radius\Radacct', 'username', 'guid' );
	}


	/**
	 * Do a join on the radacct_new table, foreign key is username.
	 * transaction can have many radacct
	 * @return mixed
	 */
	public function radacctNew()
	{
		return $this->hasMany( '\App\Models\Radius\RadacctNew', 'username', 'guid' );
	}


	/**
	 * Do a join on the radreply table, foreign key is username.
	 * transaction can have many radreply
	 * @return mixed
	 */
	public function radreply()
	{
		return $this->hasMany( '\App\Models\Radius\Radreply', 'username', 'guid' );
	}


	/**
	 * Do a join on the radcheck table, foreign key is username.
	 * transaction can have many radreply
	 * @return mixed
	 */
	public function radcheck()
	{
		return $this->hasMany( '\App\Models\Radius\Radcheck', 'username', 'guid' );
	}

}