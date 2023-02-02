<?php

namespace Internal\Database\Adapters;

use mysqli;

class MySqlAdapter extends BaseAdapter
{
	/**
	 * Escape string
	 */
	public function Escape(string $string): string
	{
		return $this->connection->real_escape_string($string);
	}

	/**
	 * Connect
	 */
	public function __construct(array $data)
	{
		$this->connection = new mysqli($data['host'], $data['username'], $data['password'], $data['database'], $data['port']);
	}

	/**
	 * Select data
	 */
	public function Get(string $tableName, array $selects, array $filter): array
	{
		$sql = "SELECT " . join(', ', $selects) . " FROM $tableName";

		if (!empty($filter)) {
			$stringFilter = [];

			foreach ($filter as $key => $value) {
				array_push($stringFilter, $key . " = " . $this->Escape($value));
			}

			$sql .= " WHERE " . join(' AND ', $stringFilter);
		}

		$query = $this->connection->query($sql);

		$results = [];

		while (
			$result = $query->fetch_assoc()
		) {
			array_push($results, $result);
		}

		return $results;
	}
}
