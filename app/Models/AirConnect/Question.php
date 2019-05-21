<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Question extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'question';
	public $fillable = ['portal', 'name', 'type', 'pattern', 'required', 'order', 'status'];
	/**
	 * question belongs to portal
	 * @return mixed
	 */
	public function questionPortal()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Portal', 'id' );
	}

	/**
	 * question belongs to form
	 * @return mixed
	 */
	public function forms()
	{
		return $this->belongsToMany( '\App\Models\AirConnect\Form' );
	}

	/**
	 * Do a join on the question_attribute table, foreign key is ids.
	 * question can have many question_attribute
	 * @return mixed
	 */
	public function attributes()
	{
		return $this->hasMany( '\App\Models\AirConnect\QuestionAttribute', 'ids' );//->orderBy('name', 'asc');
	}

	/**
	 * Cascade deleting attributes
	 */
	protected static function boot() {
		parent::boot();

		static::deleting(function($question) { // before delete() method call this
			$question->attributes()->delete();
			// do the rest of the cleanup...
		});
	}
}