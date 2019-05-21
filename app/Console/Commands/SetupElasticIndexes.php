<?php

namespace app\Console\Commands;

use App\Models\AirConnect\User as UserModel;
use App\Models\AirConnect\Gateway as GatewayModel;
use App\Models\AirHealth\Hardware as HardwareModel;
//use App\Models\AirConnect\Message as MessageModel;
use Elasticsearch\Client;
use Illuminate\Console\Command;
use App\Helpers\UrlHelper;

/**
 * This command is responsible on indexing existing data into elasticsearch
 * Class ReindexCommand
 * @package app\Console\Commands
 */
class  SetupElasticIndexes extends Command
{
	protected $signature = "setup:elastic {index}";
	protected $description = "Indexing to elasticsearch";
	private $elasticClient;
	private $clientHost;

	private $indexModel = [
		'gateway'	=> GatewayModel::class,
		'hardware'	=> HardwareModel::class,
		'user'		=> UserModel::class,

//		'messages'	=> MessageModel::class
	];

	// Searchable Models
	private $models = [];
	private $indexes = [];

	/**
	 * ReindexCommand constructor.
	 * @param Client $client
	 */
	public function __construct(Client $client)
	{
		parent::__construct();

		$this->models 	= array_values($this->indexModel);
		$this->indexes 	= array_keys($this->indexModel);
		$this->elasticClient = $client;
		$this->clientHost = config('services.search.hosts')[0];
	}

	/**
	 * This will go through all models and index their data in chunks of 1000 rows to avoid memory issues
	 * Setup:elastic will need a parameter, if all is passed then it will recreate all indexes otherwise it will setup only the one sent as a parameter
	 */
	public function handle()
	{
		$parameter = $this->argument('index');

		if($parameter == 'all')
			$this->createAllIndexes();
		else
			$this->createIndex($parameter);


	}

	/**
	 * This will delete the index passed as a parameter and recreate it
	 * @param $index
	 */
	private function createIndex($index)
	{
		if(in_array($index, $this->indexes))
		{
			$this->line("Deleting index {$index}!");
			try { UrlHelper::callClient($this->clientHost . '/'. $index, 'DELETE'); }	catch(\Exception $e){  $this->error($e->getMessage()); }

			switch ($index)
			{
				case 'gateway':
					$this->createGatewayIndex();
					break;
				case 'hardware':
					$this->createHardwareIndex();
					break;
				case 'user':
					$this->createUserIndex();
					break;
	/*			case 'messages':
					$this->createMessagesIndex();
					break;*/
				default:

			}
			$this->indexData($index);
		}
		else
		{
			$this->error("Unknown index name, please try again...");
		}
	}

	/**
	 * This will delete all indexes and recreate them
	 */
	private function createAllIndexes()
	{
		$this->line('Re-creating indexes');
		$this->deleteIndexes();

		$this->createHardwareIndex();
		$this->createGatewayIndex();
		$this->createUserIndex();
		//$this->createMessagesIndex();

		$this->indexData();

		$this->line("Done!");
	}

	/**
	 * Delete existing indexes, index names are stored in $this->indexes
	 */
	private function deleteIndexes()
	{
		$this->line("Deleting all indexes!");
		foreach ($this->indexes as $index)
			try { UrlHelper::callClient($this->clientHost . '/'. $index, 'DELETE'); }	catch(\Exception $e){  $this->error($e->getMessage());  }

	}

	/**
	 * Indexing data into elastic indexes
	 */
	private function indexData($index = null)
	{
		$this->line('Indexing Starting . It might take a while...');

		if(!is_null($index))
			$this->models = [$this->indexModel[$index]];

		foreach ($this->models as $model) {
			$counter = 0;
			$this->info("\n --- Indexing: {$model} ---");

			// Chunking data in 10000 and indexing them 1 by 1
			$model::chunk(10000, function ($rows) use (&$counter) {
				foreach ($rows as $row) {
					$this->elasticClient->index([
						'index' => $row->getSearchIndex(),
						'type' => $row->getSearchType(),
						'id' => $row->id,
						'body' => $row->toSearchArray(),
					]);
					// showing the counter
					$this->output->write('.');
					$counter++;
				}
				$this->output->write("\n" . $counter . "\n");
			});

			$this->info("\n---{$counter} rows indexed ---");
		}
	}

	/**
	 * Creates User index
	 */
	private function createUserIndex()
	{
		$client = new \GuzzleHttp\Client();

		$client->request('PUT', $this->clientHost . '/user', [
			'json' => [
				'mappings' =>
					[
						'user' =>
							[
								'properties' =>
									[
										'user' =>
											[
												'type' => 'text',
												'analyzer' => 'email',
												'search_analyzer' => 'keyword',
											],
										'mac' =>
											[
												'type' => 'text',
												'analyzer' => 'mac_edge_ngram_analyzer',
												'search_analyzer' => 'keyword',
											],
										'id' =>
											[
												'type' => 'integer',
											],
										'site' =>
											[
												'type' => 'integer',
											],
										'created' =>
											[
												'type' => 'date',
												'format' => 'yyyy-MM-dd HH:mm:ss',
											],
										'updated' =>
											[
												'type' => 'date',
												'format' => 'yyyy-MM-dd HH:mm:ss',
											],
									],
							],
					],
				'settings' =>
					[
						'analysis' =>
							[
								'filter' => [
									'email' => [
										'type' => 'pattern_capture',
										'preserve_original' => true,
										'patterns' => [
											"([^@]+)",
											"(\\p{L}+)",
											"(\\d+)",
											"@(.+)"
										]
									]
								],

								'analyzer' =>
									[
										'mac_edge_ngram_analyzer' =>
											[
												'tokenizer' => 'mac_edge_ngram_tokenizer',
												'filter' =>
													[
														0 => 'lowercase',
													],
											],
										'email' => [
											'tokenizer' => 'uax_url_email',
											'filter' => [
												'email', 'lowercase', 'unique'
											]
										]
									],
								'tokenizer' =>
									[
										'mac_edge_ngram_tokenizer' =>
											[
												'type' => 'edgeNGram',
												'min_gram' => '2',
												'max_gram' => '17',
											],
									],
							],
					],
			]
		]);
	}

	/**
	 * Create Hardware Index
	 */
	private function createHardwareIndex()
	{
		$client = new \GuzzleHttp\Client();
		$client->request('PUT', $this->clientHost . '/hardware', [
			'json' => [
				'mappings' =>
					[
						'hardware' =>
							[
								'properties' =>
									[
										'mac' =>
											[
												'type' => 'text',
												'analyzer' => 'mac_edge_ngram_analyzer',
												'search_analyzer' => 'keyword',
											],
										'ip' =>
											[
												'type' => 'text',
												'analyzer' => 'ip_edge_ngram_analyzer',
												'search_analyzer' => 'keyword',
											],
										'id' =>
											[
												'type' => 'integer',
											],
										'site' =>
											[
												'type' => 'integer',
											],
										'location' =>
											[
												'type' => 'geo_point',
											],
										'created' =>
											[
												'type' => 'date',
												'format' => 'yyyy-MM-dd HH:mm:ss',
											],
										'updated' =>
											[
												'type' => 'date',
												'format' => 'yyyy-MM-dd HH:mm:ss',
											],
									],
							],
					],
				'settings' =>
					[
						'analysis' =>
							[
								'analyzer' =>
									[
										'mac_edge_ngram_analyzer' =>
											[
												'tokenizer' => 'mac_edge_ngram_tokenizer',
												'filter' =>
													[
														0 => 'lowercase',
													],
											],
										'ip_edge_ngram_analyzer' =>
											[
												'tokenizer' => 'ip_edge_ngram_tokenizer',
											],
									],
								'tokenizer' =>
									[
										'mac_edge_ngram_tokenizer' =>
											[
												'type' => 'edgeNGram',
												'min_gram' => '2',
												'max_gram' => '17',
											],
										'ip_edge_ngram_tokenizer' =>
											[
												'type' => 'edgeNGram',
												'min_gram' => '1',
												'max_gram' => '14',
											],
									],
							],
					],
			]
		]);
	}

	/**
	 * Create Gateway Index
	 */
	private function createGatewayIndex()
	{
		$client = new \GuzzleHttp\Client();
		$client->request('PUT', $this->clientHost . '/gateway', [
			'json' => [
				'mappings' =>
					[
						'gateway' =>
							[
								'properties' =>
									[
										'mac' =>
											[
												'type' => 'text',
												'analyzer' => 'mac_edge_ngram_analyzer',
												'search_analyzer' => 'standard',
											],
										'ip' =>
											[
												'type' => 'text',
												'analyzer' => 'ip_edge_ngram_analyzer',
												'search_analyzer' => 'keyword',
											],
										'id' =>
											[
												'type' => 'integer',
											],
										'site' =>
											[
												'type' => 'integer',
											],
										'location' =>
											[
												'type' => 'geo_point',
											],
										'created' =>
											[
												'type' => 'date',
												'format' => 'yyyy-MM-dd HH:mm:ss',
											],
										'updated' =>
											[
												'type' => 'date',
												'format' => 'yyyy-MM-dd HH:mm:ss',
											],
									],
							],
					],
				'settings' =>
					[
						'analysis' =>
							[
								'analyzer' =>
									[
										'mac_edge_ngram_analyzer' =>
											[
												'tokenizer' => 'mac_edge_ngram_tokenizer',
												'filter' =>
													[
														0 => 'lowercase',
													],
											],
										'ip_edge_ngram_analyzer' =>
											[
												'tokenizer' => 'ip_edge_ngram_tokenizer',
											],
									],
								'tokenizer' =>
									[
										'mac_edge_ngram_tokenizer' =>
											[
												'type' => 'edgeNGram',
												'min_gram' => '2',
												'max_gram' => '17',
											],
										'ip_edge_ngram_tokenizer' =>
											[
												'type' => 'edgeNGram',
												'min_gram' => '1',
												'max_gram' => '14',
											],
									],
							],
					],
			]
		]);
	}

	/**
	 * Creates Messages index

	private function createMessagesIndex()
	{
		$client = new \GuzzleHttp\Client();
		$client->request('PUT', $this->clientHost . '/messages', [
			'json' => [
				'mappings' =>
					[
						'messages' =>
							[
								'properties' =>
									[
										'id' =>
											[
												'type' => 'integer',
											],
										'site' =>
											[
												'type' => 'integer',
											],
										'user_id' =>
											[
												'type' => 'integer',
											],
										'role_id' =>
											[
												'type' => 'integer',
											],
										'type' =>
											[
												'type' => 'text',
												'fielddata' => true,
											],
										'created' =>
											[
												'type' => 'date',
												'format' => 'yyyy-MM-dd HH:mm:ss',
											],
									],
							],
					],
			]
		]);
	}
	 */

}