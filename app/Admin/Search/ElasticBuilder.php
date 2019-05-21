<?php

namespace app\Admin\Search;

/**
 * This Builder is to mimic the datatable query builder using the elastic search
 * Class ElasticBuilder
 * @package app\Admin\Modules\Guests
 */
class ElasticBuilder
{
	public $elasticSearch;
	public $skip			= 0;
	public $show			= 0;
	public $search 			= '';
	public $total   		= null;
	public $data 			= [];
	public $orderColumn 	= '';
	public $orderDirection  = '';

	/**
	 * Building the data and the total
	 * ElasticBuilder constructor.
	 * @param $datatableData
	 * @param $elasticSearch
	 */
	public function __construct($datatableData, $elasticSearch, $orderColumn = 'updated', $orderDirection  = 'desc')
	{
		$this->data 	= $datatableData[0];
		$this->total 	= $datatableData[1];

		$this->elasticSearch 	= $elasticSearch;
		$this->orderColumn 	 	= $orderColumn;
		$this->orderDirection 	= $orderDirection;
	}

	/**
	 * Retrieving the total for the datatable
	 * For a strange reason Chumper is calling this function twice
	 * @return mixed
	 */
	public function count()
	{
		return $this->total;
	}

	/**
	 * This is called to retrieve data for the datatable where the value is the amount to show in the DT
	 * @param $value
	 * @return $this
	 */
	public function take($value)
	{
		$this->show = $value;
		list($this->data, $this->total) = $this->elasticSearch->search($this->search, $this->skip, $this->show, $this->orderColumn, $this->orderDirection);
		return $this;
	}

	/**
	 * This will return the data for the datatable
	 * @return mixed
	 */
	public function get()
	{
		return $this->data;
	}

	/**
	 * Skip is used for the pagination, Value is the number of records to skip
	 * @param $value
	 * @return mixed
	 */
	public function skip($value)
	{
		$this->skip = $value;
		return $this;
	}

	/**
	 * This is used to setup the order by clause
	 * @param $column
	 * @param string $direction
	 * @return $this
	 */
	public function orderBy($column, $direction = 'desc')
	{
		$this->orderColumn = $column;
		$this->orderDirection = $direction;
		return $this;
	}

	/**
	 * This is normally the where clause in the DB we will use it to store the search value
	 */
	public function where()
	{
		$this->search = \Request::all()['sSearch'];
	}

	/**
	 * The following functions are needed to make dummy so Chumper will be executed with no errors
	 */
	public function orWhere(){}
	public function orWhereRaw(){}

}