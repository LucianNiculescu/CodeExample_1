<?php
namespace App\Admin\Search;

use Elasticsearch\Client;

/**
 * Put the model name in the $indexes array
 * Put the all fields you want to search for in there
 * Class ElasticsearchRepository
 * @package App\Admin\Search
 */
class ElasticsearchRepository implements SearchRepository
{
	private $searchClient;
	private $indexes 	= [];// ['user', 'gateway', 'hardware'];
	private $fields 	= ['user', 'name', 'type', 'ip', 'mac', 'nasid'];
	private $source		= ['name', 'id', 'mac', 'ip', 'user', 'type', 'created', 'updated', 'status'];

	/**
	 * Constructor sets the private variable
	 * ElasticsearchRepository constructor.
	 * @param Client $client
	 */
	public function __construct(Client $client) {
		$this->searchClient = $client;

		$searchType = session('admin.search_type') ?? 'all';

		// Adding indexes depending on the permission

		if( \Gate::allows('access', 'manage.guests') and in_array($searchType, ['all', 'user']))
			$this->indexes[] = 'user';

		if(( \Gate::allows('access', 'networking.gateways') or \Gate::allows('access', 'all-gateways'))  and in_array($searchType, ['all', 'gateway']))
			$this->indexes[] = 'gateway';

		if( \Gate::allows('access', 'networking.hardware')  and in_array($searchType, ['all', 'hardware']))
			$this->indexes[] = 'hardware';

	}

	/**
	 * Search Elasticsearch indexes
	 * @param String $query
	 * @param int $from
	 * @return array
	 */
	public function search(String $query = "" , $from  = 0, $size = 10, $column = 'updated',  $direction = 'desc')
	{

		// if no indexes then user is not allowed to search
		if(empty($this->indexes))
			return [
				'took' => 0,
				'hits' => [
					'total' => 0,
					'hits'  => []
				]
			];

		$query = self::escapeTerm($query);

		// Doing the search for the query in the fields and indexes
		$items = $this->searchClient->search([
			// Search into these indexes
			'index' => $this->indexes,
			'type' => $this->indexes,
			'body' => [
				"sort" => [ $column => [ "order" => $direction]],
				// retrieve only the fields in $this->source
				'_source' => $this->source,
				'query' => [
					'bool' => [
						// Search only these fields with the lowercase query
						'must' => [
							'query_string' => [
								'fields' => $this->fields,
								'query' => '*' . strtolower($query) . '*'
							]
						],
						// Don't get deleted records
						'must_not' =>[
							'match'	=>	[
								'status' =>	'deleted'
							]
						],
						// Filter on sites in the estate
						'filter' => [
							'terms'	=> [
								'site' => session('admin.site.estate')
							]
						]
					],
				],
			],
			// This is for paging
			'from'	=> $from
		]);

		return $items;
	}


	/**
	 * Increase max_result_window for the user index
	 * ex.
	 * curl -XPUT "http://10.0.0.234:9200/user/_settings" -H 'Content-Type: application/json' -d'
	 * { "index" : { "max_result_window" : 1000000000} }'
	 * @param $index
	 * @param bool $max
	 */
	public static function adjustMaxResultWindow($index, $max = false)
	{
		// Getting elasticsearch server address from the services config
		$clientHost = config('services.search.hosts')[0];

		// If no value is sent then default max_result_window is 10000 else get the value from the services config file
		$value = (!$max) ? 10000 : config('services.search.max_result_window')[$index];

		// Calling Guzzle to adjust the max_result_window
		$client = new \GuzzleHttp\Client();
		$client->request('PUT', $clientHost . "/{$index}/_settings", [
			'json' => [
				"index"	=> [
					"max_result_window" => $value
				]
			]
		]);
	}


	/**
	 * Escapes the following terms (because part of the query language)
	 * + - && || ! ( ) { } [ ] ^ " ~ * ? : \ < >.
	 *
	 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html#_reserved_characters
	 *
	 * @param string $term Query term to escape
	 *
	 * @return string Escaped query term
	 */
	public static function escapeTerm($term)
	{
		$result = $term;
		// \ escaping has to be first, otherwise escaped later once again
		$chars = ['\\', '+', '-', '&&', '||', '!', '(', ')', '{', '}', '[', ']', '^', '"', '~', '*', '?', ':', '/', '<', '>'];
		foreach ($chars as $char) {
			$result = str_replace($char, '\\'.$char, $result);
		}

		// trimming result
		return trim($result);
	}
}