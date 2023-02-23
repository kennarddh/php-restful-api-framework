<?php

namespace Internal\Database\Adapters;

use Exception;
use Internal\Logger\Logger;
use MongoDB\Client;
use TypeError;

class MongoDBAdapter extends BaseAdapter
{
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
	 * Connect
	 */
	public function __construct(array $data)
	{
		$this->connection = new Client(
			$data['uri'],
			isset($data['uriOptions']) ? $data['uriOptions'] : [],
			isset($data['driverOptions']) ? $data['driverOptions'] : []
		);

		$database = $data['database'];

		$this->connection = $this->connection->$database;
	}

	/**
	 * Select data
	 */
	public function Get(string $collectionName, array $selects, array $filter): array
	{
		$collection = $this->connection->$collectionName;

		$projection = ['_id' => 0];

		foreach ($selects as $key) {
			$projection[$key] = 1;
		}

		$cursor = $collection->find(
			$filter,
			[
				'projection' => $projection,
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
	public function Insert(string $collectionName, array $data): bool
	{
		$collection = $this->connection->$collectionName;

		try {
			$insertOneResult = $collection->insertOne($data);
		} catch (Exception $e) {
			Logger::Log('error', "MongoDB Insert Error\n" . $e->__toString());

			return false;
		}

		return true;
	}

	/**
	 * Update data
	 */
	public function Update(string $collectionName, array $data, array $filter): bool
	{
		$collection = $this->connection->$collectionName;

		try {
			$updateManyResult = $collection->updateMany($filter, ['$set' => $data]);
		} catch (Exception $e) {
			Logger::Log('error', "MongoDB Update Error\n" . $e->__toString());

			return false;
		}

		return true;
	}

	/**
	 * Delete data
	 */
	public function Delete(string $collectionName, array $filter): bool
	{
		$collection = $this->connection->$collectionName;

		try {
			$deleteManyResult = $collection->deleteMany($filter);
		} catch (Exception $e) {
			Logger::Log('error', "MongoDB Delete Error\n" . $e->__toString());

			return false;
		}

		return true;
	}
}
