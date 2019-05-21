<?php


namespace App\Admin\Modules\Gateways\Types;


class SuperType
{
	protected $gateway;
	protected $onlineNowInterpreter;
	protected $logsInterpreter;

	/**
	 *
	 * SuperType Constractor
	 * @param $gateway
	 */
	function __construct($gateway)
	{
		$this->gateway = $gateway;
	}

	/**
	 * Interpretting the result to match the way the datatable shows them in the UI
	 * @param $inputArray
	 * @param $interpretArray
	 * @return array
	 */
	protected function interpretResult($inputArray, $interpretArray)
	{
		// If interpretArray is empty then no need to interpret the data
		if(empty($interpretArray))
			return $inputArray;

		$result = [];
		$interpretKeys = array_keys($interpretArray);

		// Looping into $inputArray , example $devices connected to the gateway or Logs
		foreach ($inputArray as $row)
		{
			$newResult = [];
			// Looping into each device and change its key to match the onlinenow data
			foreach ($row as $key=>$value)
			{
				// Converting the inputArray keys to be the same like the interpret Array Key
				if (in_array($key, $interpretKeys))
					$newResult[$interpretArray[$key]] = $row[$key];
				else
					$newResult[$key] = $row[$key];
			}
			$result[] = $newResult ?? [];
		}

		return $result;
	}
}