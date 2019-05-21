<?php

namespace App\Admin\Search;

use Elasticsearch\Client;
use App\Exceptions\Errors\Logic as Errors;

/**
 * This class is responsible of updating the elasticsearch index if a model is saved or deleted
 * Class ElasticsearchObserver
 * @package App\Admin\Search
 */
class ElasticsearchObserver
{
	private $elasticsearch;

	/**
	 * Constructor setting the private elasticsearch client
	 * ElasticsearchObserver constructor.
	 * @param Client $elasticsearch
	 */
	public function __construct(Client $elasticsearch)
	{
		$this->elasticsearch = $elasticsearch;
	}

	/**
	 * Once the Model is saved it updates the elasticsearch index
	 * @param $model
	 */
	public function saved($model)
	{
		try{
			$this->elasticsearch->index([
				'index' => $model->getSearchIndex(),
				'type' 	=> $model->getSearchType(),
				'id' 	=> $model->id,
				'body' 	=> $model->toSearchArray(),
			]);
		}
		catch(\Exception $e){
			Errors::reportError($e);
		}
	}

	/**
	 * When deleting from the model the index is updated
	 * @param $model
	 */
	public function deleted($model)
	{
		try{
			$this->elasticsearch->delete([
				'index' => $model->getSearchIndex(),
				'type' 	=> $model->getSearchType(),
				'id' 	=> $model->id,
			]);
		}
		catch(\Exception $e){
			Errors::reportError($e);
		}
	}
}