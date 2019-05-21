<?php

namespace App\Admin\Search;

use Illuminate\Database\Eloquent\Collection;

interface SearchRepository
{
	/**
	 * @param string $query = ""
	 * @return Collection
	 */
	public function search(String $query = "" , $from  = 0, $size = 10, $column = 'id',  $direction = 'asc');

}