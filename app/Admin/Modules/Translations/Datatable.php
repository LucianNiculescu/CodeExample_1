<?php
/**
 * Created by PhpStorm.
 * User: sheri
 * Date: 5/31/2016
 * Time: 01:41 PM
 */

namespace App\Admin\Modules\Translations;
use \App\Admin\Helpers\BasicDatatable;
use \App\Helpers\Language;

class Datatable extends BasicDatatable
{

	/**
	 * Setting up datatable and send it back
	 * @return mixed
	 */
	public static function getTable()
	{
		$table = parent::getBasicTable('translations');

		// We always want to see the key and type
		$table =
			$table->addColumn(
					trans('admin.key'),
					trans('admin.type')
					);

		// Add each language
		foreach (Language::getLanguages() as $language) {
			$table =
				$table->addColumn(
					trans('admin.'. $language)
				);
			}

		// Set the initial sort order
		$table = $table->setOptions( [
					'order' => [0, "asc"],
				]);

		return $table ->noScript();
	}

	/**
	 * Making the Datatable
	 * showColumns is a list of the titles of the columns
	 * addColumn is adding a column one by one, the return of the call back function determines how the output in this column will look like
	 * searchColumns list of searchable columns
	 * orderColumns list of columns to order by
	 * @param $query
	 * @return mixed
	 */
	public static function makeTable($query)
	{
		// Get the list of languages from the database
		$languages = Language::getLanguages();

		// Build lists for which columns to show and for the search and order columns (which will be the same)
		$show = ['key', 'type'];
		$searchAndOrder = ['key'];

		// Add the key and type columns because they don't need to cater for special characters
		$tableQuery = self::query( $query )
			->addColumn( 'key'	, function( $trans ) { return $trans->key ;	} )
			->addColumn( 'type'	, function( $trans ) { return $trans->type ;} );

		// Go through the list of languages and add each to column lists.
		// Also add the column data to the table after handling special characters.
		foreach ($languages as $language) {
			$show[] = $language;
			$searchAndOrder[] = $language;

			$tableQuery = $tableQuery
				->addColumn( $language	, function( $trans ) use ($language) { return htmlspecialchars($trans->$language) ; } );
		}

		// The type column is last for search columns and order by
		$searchAndOrder[] = 'type';

		// Use the constructed lists to set which columns to show, search and order by
		$tableQuery = $tableQuery
			->showColumns( $show)
			->searchColumns	($searchAndOrder)
			->orderColumns 	($searchAndOrder);

		// Finally, make the datatable and return it
		return $tableQuery->make();

	}
}