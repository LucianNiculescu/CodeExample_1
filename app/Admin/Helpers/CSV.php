<?php

namespace App\Admin\Helpers;

use \League\Csv\Reader;
use \League\Csv\Writer;

/**
 * Class CSV: is responsible of downloading and uploading CSV files and more in the future
 * @package App\Admin\Helpers
 */
class CSV
{
	/**
	 * Uploads the file to the database
	 * @param $file // is the file location on the server
	 * @param $modelName // is the name of the model i.e. 'App\Models\AirConnect\Translation'
	 * @param bool $encode // is used to encode HTML code
	 * @return \Illuminate\Http\RedirectResponse|int How to call it ?
	 *
	 * How to call it ?
	 * return \App\Admin\Helpers\CSV::uploadToDB('/var/www/app/translations.csv', 'App\Models\AirConnect\Translation');
	 */
	public static function uploadToDB($file, $modelName, $encode = true)
	{
		$model = new $modelName;

		// Getting the column names in an array
		$keys = $model->getFillable();

		// $file now is passed in a form upload
		$csvReader = Reader::createFromPath($file);

		// Reading from the CSV
		$csvData = $csvReader->fetchAll();

		// Loop in the CSV file
		foreach ($csvData as $index => $row) {
			// index 0 is for the headers which are already saved in Keys
			if ($index != 0) {
				// Looping in the row and filling the dbRow as key => value
				foreach ($row as $key => $dbColumn) {
					// Default decode is true but for translations it is off
					if ($encode)
						$dbColumn = htmlspecialchars($dbColumn);

					// ["en"=>"yes" , "fr"=>"oui" ...etc]
					$dbRow[$keys[$key]] = $dbColumn;
				}

				// allRows is an array of arrays to be inserted to the DB in one go
				$allRows[] = $dbRow;
			}
		}

		// Delete all from the DB
		$model->truncate();

		// Insert all Rows
		$inserted = $model->insert($allRows);

		if ($inserted)
			// Ajax returns 1 if successful
			if(\Request::ajax())
				return 1;
		else
			// Ajax returns 0 if failed
			if(\Request::ajax())
				return 0;

		return true;
	}

	/**
	 * Downloads data from the model into a csv file
	 * @param $modelName // is the name of the model i.e. 'App\Models\AirConnect\Translation'
	 * @param $file
	 * @param $decode //to decode HTML
	 *
	 * How to use it?
	 * return \App\Admin\Helpers\CSV::downloadFromDB('\App\Models\AirConnect\Translation', 'trans.csv');
	 */
	public static function downloadFromDB($modelName, $file, $decode = true)
	{
		$model = new $modelName;
		// Prep the data
		$allData = $model::all();

		// Table name is needed to get it's headers
		$table 	= with(new $model)->getTable();

		// Create the CSV file in memory
		$csvWriter = Writer::createFromFileObject(new \SplTempFileObject());

		// Setting up the newline , delimiter and encoding
		$csvWriter->setNewline("\r\n");
		$csvWriter->setDelimiter(',');
		$csvWriter->setOutputBOM(Reader::BOM_UTF8);

		// Create the headers
		$csvWriter->insertOne(\Schema::getColumnListing($table));

		// Insert rows
		foreach ($allData as $row)
			if ($decode)
				$csvWriter->insertOne(array_map([CSV::class, 'htmlDecode'], $row->toArray()));
			else
				$csvWriter->insertOne($row->toArray());
		// Output
		$csvWriter->output($file);

	}

	/**
	 * Decodes a html string
	 * @param string $str
	 * @return string
	 */
	public static function htmlDecode($str) {
		return htmlspecialchars_decode($str)  ;
	}

    /**
     * Creates a file with the given array
     * @param array $data // results passed into the function as array
     * @param string $filename // can contain the path within the actual name (eg: '/csv-reports/filename.csv')
     * @return bool // true after the file has been created
     */
    public static function createFile(array $data, $filename)
    {
        // Create the CSV file in a file
        $csvWriter = Writer::createFromPath(new \SplFileObject(public_path().'/'.$filename, 'w+'), 'w+');
		if(!isset($csvWriter)) {
			if (config('app.debug'))
				\Log::info("Could not create the file. Writer::createFromPath() failed.");
			return false;
		}

		// Setting up the newline , delimiter and encoding
		$csvWriter->setNewline("\r\n");
		$csvWriter->setDelimiter(',');
		$csvWriter->setOutputBOM(Reader::BOM_UTF8);

		// Create the headers
		$header = self::getHeaderFromArray($data);
		if($header) {
			$csvWriter->insertOne($header);
			// Insert rows
			foreach ($data as $row)
				$csvWriter->insertOne($row);

			return true;
		}
		return false;
    }

    /**
     * Retrieves the header from the given array (used as the first line of the csv file)
     * @param array $data // unformated array with the records from the models
     * @return mixed $header // the formated data
     */
    public static function getHeaderFromArray($data)
    {
		$header = [];
		if(!empty($data[0]))
			foreach($data[0] as $k => $val)
				$header[]= ucfirst($k);

        return $header;
    }
}