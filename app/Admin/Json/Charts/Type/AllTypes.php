<?php


namespace App\Admin\Json\Charts\Type;


class AllTypes
{
	protected $type;
	protected $mac;
	protected $siteId;
	protected $startDate;
	protected $endDate;
	protected $hourly;
	protected $data;


	public function __construct($type, $mac, $siteId, $startDate, $endDate, $hourly)
	{
		$this->type = $type;
		$this->mac = $mac;
		$this->siteId = $siteId;
		$this->startDate = $startDate;
		$this->endDate = $endDate;
		$this->hourly = $hourly;
		$this->dataBuilder();
		$this->getData();
	}

	/**
	 * Sets the getData function up to be used by classes that extend AllTypes
	 */
	protected function getData(){

	}

	/**
	 * Sets the dataBuilder function to be used by classes that extend AllTypes
	 */
	protected function dataBuilder(){

	}


	/**
	 * Get the keys as an array from data
	 * @param $data
	 * @return array
	 */
	private function getKeys($data)
	{
		$keys = array();
		foreach( $data as $key => $value )
			$keys[] = $key;

		return $keys;
	}

	/**
	 * Will take in data and fill in 0s for any date that does not exist
	 * @param array $data 		The data to fill with 0s for any missing dates
	 * @param string $from 		Y-m-d date to go from
	 * @param string $to 		Y-m-d date to go to
	 * @param string $dateName 	Name of the key in the data that contains the Y-m-d date
	 * @param bool $hourly 		Do we want to show the breakdown of hours with the days
	 * @return array 			The data filled with 0s for any missing dates
	 */
	protected function fillDates($from, $to, $dateName='report_date', $hourly=false )
	{
		$data = $this->data;

		// If we have no date to infill just return the data
		if( !isset( $data[0][$dateName] ) )
			return $data;

		if( $hourly ) {
			$expectedInterval = '1 hour';
			$expectedDateFormat = "Y-m-d H:i:s";
		}else {
			$expectedInterval = '1 day';
			$expectedDateFormat = "Y-m-d";
		}

		// The data we will return
		$updatedData = array();

		// Get the data we are trying to fill
		$keys = $this->getKeys( $data[0] );

		// Change the date to the array key
		foreach( $data as $info )
			$updatedData[$info['report_date']] = $info;

		// Create the dates
		$start = new \DateTime( $from );
		$end = new \DateTime( $to );
		$interval = \DateInterval::createFromDateString($expectedInterval);
		$period = new \DatePeriod($start, $interval, $end);

		// Loop through the dates and add data for anything that needs it
		foreach( $period as $dt )
			// If we have no data for this date
			if( !isset($updatedData[ $dt->format( $expectedDateFormat ) ] ))
				// Set each key to 0
				foreach( $keys as $key )
					$updatedData[ $dt->format( $expectedDateFormat ) ][$key] = 0;

		// Sort the data by the key
		ksort($updatedData);

		// Return the data
		return $updatedData;
	}


}