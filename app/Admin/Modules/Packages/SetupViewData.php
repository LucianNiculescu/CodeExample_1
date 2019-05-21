<?php

namespace App\Admin\Modules\Packages;

use \App\Admin\Helpers\BasicDatatable;
use \App\Models\AirConnect\Package;
use \App\Models\AirConnect\Attribute;
use \App\Helpers\CurrencyHelper;
use \App\Helpers\DateTime;
use \App\Helpers\FileHelper;
use \App\Models\AirConnect\Translation;
use App\Transformers\AttributeTransformer;
use App\Admin\Modules\Pms\Logic as Pms;
use App\Admin\Modules\Packages\Logic as Packages;


class SetupViewData
{
	/**
	 * @TODO Remove to more logical place
	 * @var array
	 */
	public static $hideFromAttributes = ['downstream', 'upstream', 'duration', 'facebook_like', 'facebook_share', 'facebook_message', 'title', 'placeholder', 'redirect', 'redirect-url', 'duration_type'];

	/**
	 * Returns the data to pass to the view to build
	 * the packages index datatable
	 *
	 * @return array
	 */
	public static function clientSideDatatable()
	{
		// Get packages and format rows
		$packages 	= Datatable::getPackagesDatatable();
		$rows		= self::formatRowsForDatatable($packages);

		// Get field names for columns (or default if no results)
		$columns = count($rows) > 0
					? array_fill_keys( array_keys(array_flip( array_keys((array) $rows->first()->toArray()) )), "")
					: array_fill_keys( array_keys(array_flip(['id', 'site', 'name', 'description', 'type', 'cost', 'created', 'upstream', 'downstream', 'duration', 'currency_symbol', 'Actions'])), "");

		// Return data for view
		return [
			'title' 				=> trans('admin.packages-title'),
			'description'			=> trans('admin.packages-description'),
			'columns'				=> $columns,
			'rows' 					=> $rows,
			'tableId'				=> 'manage-packages',
			'route'				    => 'manage/packages',
			'showActions'           => BasicDatatable::showActions('manage/packages'), // Check permissions
		];
	}

	/**
	 * Formats the rows for the datatable including translations
	 *
	 * @param  \Illuminate\Database\Eloquent\Collection $rows
	 * @return array
	 */
	public static function formatRowsForDatatable($rows)
	{
		// Get the currency symbol for the site
		$currencySymbol = CurrencyHelper::getCurrencySymbol();

		foreach($rows as $k => $row)
		{
			// Replace type with translated
			$rows[$k]->type = ($row->type == 'paid' or $row->type == 'free')
								? trans( 'admin.package-type-email' )
								: trans( 'admin.package-type-' .$row->type );

			// Insert currency symbol
			$rows[$k]->currency_symbol = $currencySymbol;

			// Replace certain values to be more readable
			$rows[$k]->upstream 	= FileHelper::megabytesToReadable($row->upstream);
			$rows[$k]->downstream 	= FileHelper::megabytesToReadable($row->downstream);
			$rows[$k]->duration		= DateTime::seconds2readable($row->duration);
		}

		return $rows;
	}

	/**
	 * Returns the data to pass to the Package create view
	 *
	 * @return array
	 */
	public static function create()
	{
		$siteAttributesData = Packages::getSiteAttributesData();

		// Check if PMS is enabled or not
		Pms::pmsCheck();

		// Create a list of package types for a drop down with translated values
		$translatedPackageTypes = collect(Package::$types)->mapToAssoc(function($type) {
			return [$type, trans('admin.' . $type)];
		})->toArray();


		// Create a list of package types already in use by the site which should be unique
		$disabledPackageTypes = array_diff(Package::typesInUse()->pluck('type')->toArray(), Package::$typeMultiples);

		// Get a list of Site Attributes and transform them for use in the blade
        $attributeRecords = Attribute::list('package')->orderBy('attribute')->get()->keyBy('attribute')->forget(self::$hideFromAttributes);
        $attributes       = AttributeTransformer::transform($attributeRecords)->as('object')->into('blade');

		return [
			'title'          		=> trans('admin.create-package-title'),
			'description'    		=> trans('admin.create-package-desc'),
			'actionUrl'      		=> route('manage.packages.store'),
			'container'		 		=> false,
			'types'			 		=> $translatedPackageTypes,
			'siteAttributesData' 	=> $siteAttributesData,
			'disabledTypes'  		=> $disabledPackageTypes,
			'defaultType'	 		=> Package::$defaultType,
			'languages'		 		=> Translation::getLanguages(),
			'attributes'	 		=> $attributes,
			'currencySymbol' 		=> CurrencyHelper::getCurrencySymbol()
		];
	}

	public static function edit(Package $package)
	{
		// Build data using the create view data as a template
		$data = self::create();

		$types = [];
		return array_merge($data, [
			'title' 				=> trans('admin.edit-packages'),
			'description' 			=> trans('admin.edit-packages-desc'),
			'hiddenMethod' 			=> 'PUT',
			'actionUrl' 			=> route('manage.packages.update', $package),
			'cancelUrl' 			=> route('manage.packages.index'),
			'module'				=> $package,
			'packageAttributes'		=> $package->attributes()->get()->pluck('value', 'name')->toArray(),
			'localisedStrings'		=> $package->getPackageLocalisedStrings(),
			'gateways'				=> $package->parentSite->gateways			// Build a list of Gateways (of unique type)
												->reject(function($gateway) use (&$types) {
													if(in_array($gateway->type, $types))
														return true;

													$types[] = $gateway->type;
												})->mapToAssoc(function($gateway) {
													return [$gateway->id, implode(' - ', [$gateway->type, $gateway->name])];
												})
		]);
	}
}