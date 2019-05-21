<?php


namespace App\Models\AirConnect;

use App\Models\BaseModel;


class RoleWidget extends BaseModel
{
	protected $connection = 'airconnect';
	public $incrementing = false;
	protected $primaryKey = ['role_id', 'widget_id', 'route'];
	protected $table = 'role_widget';
	protected $fillable = ['role_id', 'widget_id', 'route', 'order','status'];


    public function widget()
    {
        return $this->belongsTo( '\App\Models\AirConnect\Widget');
    }

	/**
	 * Set the keys for a save update query.
	 * This is a fix for tables with composite keys
	 * TODO: Investigate this later on
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	protected function setKeysForSaveQuery(\Illuminate\Database\Eloquent\Builder $query) {
		if (is_array($this->primaryKey)) {
			foreach ($this->primaryKey as $pk) {
				$query->where($pk, '=', $this->original[$pk]);
			}
			return $query;
		}else{
			return parent::setKeysForSaveQuery($query);
		}
	}
}