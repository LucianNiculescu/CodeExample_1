<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class QuestionAttribute extends BaseModel
{
	protected $connection = 'airconnect';
	public $table = 'question_attribute';
	public $fillable = ['ids', 'name', 'type', 'value', 'status'];


	/**
	 * question_attribute belongs to question
	 * @return mixed
	 */
	public function question()
	{
		return $this->belongsTo( '\App\Models\AirConnect\Question', 'id' );
	}
}