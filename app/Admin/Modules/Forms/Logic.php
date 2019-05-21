<?php

namespace App\Admin\Modules\Forms;

use App\Models\AirConnect\Package as PackageModel;
use App\Models\AirConnect\FormExcludedPackages as FormExcludedPackagesModel;
use App\Helpers\ArrayHelper;

class Logic
{
	/**
	 * Getting all question types
	 * @return array
	 */
	public static function getQuestionTypes()
	{
		return [
				'checkbox'	=> trans('admin.checkbox') ,
				'colour'	=> trans('admin.colour') ,
				'date'		=> trans('admin.date') ,
				'email'		=> trans('admin.email') ,
				'number'	=> trans('admin.number') ,
				'select'	=> trans('admin.select') ,
				'text'		=> trans('admin.text') ,
			];
	}


	/**
	 * Getting all Package types as an array which the key is the type and the value is the translation
	 * @return array
	 */
	public static function getAllPackages()
	{
		$allPackages = PackageModel::select('type')
			->where('site', session('admin.site.loggedin'))
			->where('status', '!=', 'deleted')
			->whereNotIn('type', ['free', 'paid'])
			->get()
			->groupBy('type')
			->keys()
			->toArray();

		// Assigning keys to be equal to values,
		$returnArray = array_combine($allPackages, ArrayHelper::translateArray($allPackages));

		// Change register to email
		if(isset($returnArray['register']))
		{
			unset($returnArray['register']);

			if(!isset($returnArray['email']))
				$returnArray['email'] = trans('admin.email');
		}

		asort($returnArray);

		return $returnArray;

	}


	/**
	 * Getting excluded Package types as an array which the key is the type and the value is the translation
	 * @return array
	 */
	public static function getExcludedPackageTypes($id)
	{
		$excludedPackages = FormExcludedPackagesModel::where('form_id', $id)
				->select('package_type')
				->get()
				->groupBy('package_type')
				->keys()
				->toArray();

		$returnArray = array_combine($excludedPackages, ArrayHelper::translateArray($excludedPackages));

		asort($returnArray);

		return $returnArray;

	}
}