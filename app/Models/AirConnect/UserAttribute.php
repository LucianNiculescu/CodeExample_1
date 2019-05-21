<?php
namespace App\Models\AirConnect;
use App\Models\BaseModel;

class UserAttribute extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'user_attribute';
	protected $fillable = ['ids', 'name', 'type', 'value', 'status'];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['created', 'updated'];

	public static $ignoreList = ['access_token', 'password_question', 'password_answer', 'provider'];
	public static $translateList = ['gender', 'id', 'nickname', 'friends_count', 'email', 'mobile_number', 'name', 'password_answer', 'password_question', 'provider'];
	/**
	 * user_attribute belongs to user
	 * @return mixed
	 */
	public function user()
	{
		return $this->belongsTo( '\App\Models\AirConnect\User', 'ids' );
	}
}