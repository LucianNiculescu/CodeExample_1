<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class TransactionAttribute extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'transaction_attribute';
	public $fillable = ['ids', 'name', 'value', 'type', 'status', 'updated'];
	/**
	 * transaction attributes belongs to transaction
	 * @return mixed
	 */
	public function transaction()
	{
		return $this->belongsTo( 'Airconnect\Transaction', 'ids', 'id' );
	}
}