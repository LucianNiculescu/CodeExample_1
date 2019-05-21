<?php
namespace App\Models\AirConnect;

use App\Models\BaseModel;

class Widget extends BaseModel
{
	protected $connection = 'airconnect';
   // public $fillable = [];
   // public $timestamps = false;


    public function adminWidget()
    {
        return $this->hasMany( '\App\Models\AirConnect\AdminWidget', 'widget_id');
    }

    public function roleWidget()
    {
        return $this->hasMany( '\App\Models\AirConnect\RoleWidget');
    }

    public function roles()
    {
        return $this->belongsToMany( '\App\Models\AirConnect\Role');
    }
    
    public function admin()
    {
        return $this->belongsToMany( '\App\Models\AirConnect\Admin');
    }
    
}
