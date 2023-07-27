<?php

namespace Internal\Database\Adapters;

use Closure;
use Exception;
use Internal\Logger\Logger;
use MongoDB\Client;
use TypeError;

class MongoDBAdapter extends BaseAdapter
{
	private Client $client;
	/**
	 * Connect
	 */
	public function __construct(array $data)
	{
		$this->client = new Client(
			$data['uri'],
			isset($data['uriOptions']) ? $data['uriOptions'] : [],
			isset($data['driverOptions']) ? $data['driverOptions'] : []
		);

		$database = $data['database'];

		$this->connection = $this->client->selectDatabase($database);
	}

	/**
	 * Escape data
	 */
	public function Escape(array | string | bool | int | float $data): array | string | bool | int | float
	{
		switch (gettype($data)) {
			case 'boolean':
			case 'integer':
			case 'double':
				return $data;
			case 'string':
				return str_replace('$', "\u{FF04}", str_replace('.', "\u{FF0E}", $data));
			case 'array': {
					$temp = [];

					foreach ($data as $key => $value) {
						$temp[$this->Escape($key)] = $this->Escape($value);
					}

					return $temp;
				}

			default:
				throw new TypeError("Type " . gettype($data) . ' is not supported');
		}
	}

	/**
	 * Unescape data
	 */
	public function Unescape(array | string | bool | int | float $data): array | string | bool | int | float
	{
		switch (gettype($data)) {
			case 'boolean':
			case 'integer':
			case 'double':
				return $data;
			case 'string':
				return str_replace("\u{FF04}", '$', str_replace("\u{FF0E}", '.',  $data));
			case 'array': {
					$temp = [];

					foreach ($data as $key => $value) {
						$temp[$this->Unescape($key)] = $this->Unescape($value);
					}

					return $temp;
				}

			default:
				throw new TypeError("Type " . gettype($data) . ' is not supported');
		}
	}

	/**
	 * Transaction
	 */
	public function Transaction(Closure $transactionCallback): void
	{
		$session = $this->client->startSession();

		$session->startTransaction();

		try {
			$transactionCallback($session);

			$session->commitTransaction();
		} catch (Exception $exception) {
			$session->abortTransaction();

			throw $exception;
		} finally {
			$session->endSession();
		}
	}

	/**
	 * Select data
	 */
	public function Get(string $collectionName, array $selects, array $filter, ?array $options = []): array
	{
		$collection = $this->connection->selectCollection($collectionName);

		$projection = ['_id' => 0];

		foreach ($selects as $key) {
			$projection[$key] = 1;
		}

		$cursor = $collection->find(
			$filter,
			[
				'projection' => $projection,
				...$options
			]
		);

		$results = [];

		foreach ($cursor as $document) {
			if (isset($document['_id'])) {
				if (isset(((array)$document['_id'])['oid'])) {
					$document['_id'] = ((array)$document['_id'])['oid'];
				} else if (isset(((array)$document['_id'])[0])) {
					$document['_id'] = ((array)$document['_id'])[0];
				}
			}

			array_push($results, $document);
		}

		return $results;
	}

	/**
	 * Insert new data
	 */
	public function Insert(string $collectionName, array $data, ?array $options = []): bool
	{
		$collection = $this->connection->selectCollection($collectionName);

		try {
			$insertOneResult = $collection->insertOne($data, $options);
		} catch (Exception $e) {
			Logger::Log('error', "MongoDB Insert Error\n" . $e->__toString());

			return false;
		}

		return true;
	}

	/**
	 * Update data
	 */
	public function Update(string $collectionName, array $data, array $filter, ?array $options = []): bool
	{
		$collection = $this->connection->selectCollection($collectionName);

		try {
			$updateManyResult = $collection->updateMany($filter, ['$set' => $data], $options);
		} catch (Exception $e) {
			Logger::Log('error', "MongoDB Update Error\n" . $e->__toString());

			return false;
		}

		return true;
	}

	/**
	 * Delete data
	 */
	public function Delete(string $collectionName, array $filter, ?array $options = []): bool
	{
		$collection = $this->connection->selectCollection($collectionName);

		try {
			$deleteManyResult = $collection->deleteMany($filter, $options);
		} catch (Exception $e) {
			Logger::Log('error', "MongoDB Delete Error\n" . $e->__toString());

			return false;
		}

		return true;
	}
}
