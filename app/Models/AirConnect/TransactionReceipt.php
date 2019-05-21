<?php

namespace App\Models\AirConnect;

use App\Models\BaseModel;

class TransactionReceipt extends BaseModel
{
	protected $connection = 'airconnect';
    public $table = "transaction_receipt";
	public $timestamps = false;

}
