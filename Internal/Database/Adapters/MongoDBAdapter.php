<?php

namespace Internal\Database\Adapters;

use Exception;
use Internal\Logger\Logger;
use MongoDB\Client;

class MongoDBAdapter extends BaseAdapter
{
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
		return [];
	}

	/**
	 * Insert new data
	 */
	public function Insert(string $collectionName, array $data): bool|Exception
	{
		$collection = $this->connection->$collectionName;

		$insertOneResult = null;

		try {
			$insertOneResult = $collection->insertOne($data);
		} catch (Exception $e) {
			Logger::Log('info', "MongoDB Insert Error\n" . $e->__toString());
			return $e;
		}

		return true;
	}

	/**
	 * Update data
	 */
	public function Update(string $tableName, array $data, array $filter): bool
	{
		return true;
	}

	/**
	 * Delete data
	 */
	public function Delete(string $tableName, array $filter): bool
	{
		return true;
	}
}
