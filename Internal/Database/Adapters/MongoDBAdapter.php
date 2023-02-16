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
		return [];
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
			Logger::Log('info', "MongoDB Insert Error\n" . $e->__toString());

			return false;
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
