<?php
/**
 * Created by PhpStorm.
 * User: sheri
 * Date: 12-Jun-17
 * Time: 3:49 PM
 */

namespace App\Admin\Modules\Hardware;
use App\Models\AirHealth\Hardware as HardwareModel;

class Logic
{
	/**
	 * Get Gateway details
	 * Filling the array to show in the show section
	 * @param $id
	 * @return array
	 */
	public static function getDetails($id)
	{
		$ignoreHardwareColumns = ['id', 'location', 'created', 'password'];
		$results = [];

		$hardware = HardwareModel::where('id', $id)
			->with(['site' => function($q){
				$q->select('id', 'name');
			}])
			->first()->toArray();

		foreach(array_keys($hardware) as $key)
		{
			if(!in_array($key, $ignoreHardwareColumns))
			{
				if($key == 'password')
				{
					$results[$key] = '<span style="color:transparent">'.$hardware[$key].'</span>';
				}
				else if($hardware[$key] != '' and $hardware[$key] != '0' )
				{
					$results[$key] = $hardware[$key];
				}
			}
		}

		$results['site'] 	= '<strong>' . $hardware['site']['id'] . '</strong>: ' . $hardware['site']['name'];

		return $results;
	}
}