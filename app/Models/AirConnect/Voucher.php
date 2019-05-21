<?php
namespace App\Models\AirConnect;
use App\Models\BaseModel;

class Voucher extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'voucher';
	protected $fillable = ['package', 'site', 'batch',  'code',  'limit', 'concurrency', 'status', 'created', 'updated', 'start', 'stop', 'guid'];

	/**
	 * voucher belongs to package
	 * @return mixed
	 */
	public function voucherPackage()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Package', 'package' );
	}

	/**
	 * voucher belongs to site
	 * @return mixed
	 */
	public function voucherSite()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Site', 'id' );
	}

	/**
	 * voucher belongs to transaction
	 * @return mixed
	 */
	public function voucherTransaction()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Transaction', 'guid', 'guid' );
	}

	/**
	 * Scope a query to get the currently active transactions for the package
	 *
	 * @return mixed
	 */
	public function scopeActiveTransactions()
	{
		return $this->voucherTransaction()->where('status', 'Completed');
	}

	/**
	 * Do a join on the voucher_attribute table, foreign key is ids.
	 * voucher can have many voucher_attribute
	 * @return mixed
	 */
	public function voucherAttributes()
	{
		return $this->hasMany( '\App\Models\AirConnect\VoucherAttribute', 'ids' );
	}
}