<?php
namespace App\Models\AirConnect;
use App\Models\BaseModel;

class Attribute extends BaseModel
{
	protected $connection = 'airconnect';
	/**
	 * Indicate the model should not be timestamped
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * The table associated with the model
	 *
	 * @var string
	 */
	public $table = 'attribute';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'attribute',
		'type',
		'description',
		'mode',
		'insert-table'
	];

	/**
	 * The default values for attributes. Multidimensional array by the `mode` field
	 *
	 * @var array
	 */
	private static $defaults = [
		'package' => [
			'idle-timeout' 			=> 1200,
			'accounting-interval'	=> 3600
		]
	];

	/**
	 * Returns the default value for an attribute (with the provided mode) set on this model
	 *
	 * @param  string $mode
	 * @param  string $attribute
	 * @return mixed|bool
	 */
	public static function getDefaultValue($mode, $attribute)
	{
		// Set attribute name as lowercase
		$attribute = strtolower($attribute);

		// Return default value if set or (bool)false
		return isset(self::$defaults[$mode][$attribute])
				? self::$defaults[$mode][$attribute]
				: false;
	}

	/**
	 * Scope a query to retrieve a distinct list of attributes, optionally
	 * limiting by the mode field
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param  mixed $mode Optionally limit the mode field by
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeList($query, $mode = false)
	{
		// Limit the query by the mode if supplied
		if($mode !== false)
			$query->where('mode', $mode);

		return $query;
	}
}