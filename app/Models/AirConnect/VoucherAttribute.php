<?php
namespace App\Models\AirConnect;
use App\Models\BaseModel;

class VoucherAttribute extends BaseModel
{
	protected $connection = 'airconnect';
	protected $fillable = ['ids', 'name', 'type', 'value', 'status'];
	public $table = 'voucher_attribute';

	/**
	 * voucher_attribute belongs to voucher
	 * @return mixed
	 */
	public function voucher()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Voucher', 'ids' );
	}
}