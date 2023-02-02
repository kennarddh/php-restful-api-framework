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
				if (is_array($value)) {
					array_push($stringFilter, $key . " " . $value[0] . " " . $this->Escape($value[1]));
				} else {
					array_push($stringFilter, $key . " = " . $this->Escape($value));
				}
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


	/**
	 * Insert new data
	 */
	public function Insert(string $tableName, array $data): bool
	{
		$values = [];

		foreach (array_values($data) as $value) {
			if (is_string($value)) {
				array_push($values, '"' . $value . '"');
			} else {
				array_push($values, $value);
			}
		}

		$sql = "INSERT INTO $tableName (" . join(', ', array_keys($data)) . ') VALUES (' . join(', ', $values) . ')';

		$result = $this->connection->query($sql);

		return $result;
	}
}
