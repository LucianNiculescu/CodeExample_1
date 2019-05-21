<?php

namespace App\Admin\Modules\Reports\CSV;

use App\Models\AirConnect\User;

class Gender extends DefaultCSV
{

    /**
     * Generating the array with all the information used in the file
     * @return array //just add the array inside the protected member $this->reportData
     */
    protected function getReportData() {

		$gender = User::select('site', 'user_attribute.value', \DB::raw('COUNT(user_attribute.value) as genderCount'))
			->join('user_attribute', 'user_attribute.ids', '=', 'user.id')
			->whereIn('site', $this->childrenIds)
			->whereBetween('user.created', $this->fromTo)
			->where('user_attribute.name', '=', 'gender')
			->groupBy('user_attribute.value', 'site')
			->orderBy('user_attribute.value', 'desc')
			->get()
			->toArray();

		if(!empty($gender))
			return $this->getGenderArray($gender);
		else
			return [];
    }

	/**
	 * Gets the genders from database and generates the array that will be showed in the CSV file
	 * @param $gender
	 * @return mixed
	 */
	private function getGenderArray($gender) {
		$total = 0; //Set the total
		foreach( $gender as $key=>$value ) {
			$gender[$value['value']] = $gender[$key]; // Create a new key
			$gender[$value['value']] = $value['genderCount']; // Add the total to the new key
			unset( $gender[ $key ] ); // Remove the old key
			$total += $value['genderCount']; // Add the total
		}

		// Set the array in case anything is empty
		if( !isset($gender['male'])) { $gender['male'] = 0; }
		if( !isset($gender['female'])) { $gender['female'] = 0; }
		$gender[ 'total' ] = $total; // Add the total the the array

		return [$gender];
	}
}