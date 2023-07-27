<?php

namespace Internal\Database\Adapters;

use Closure;
use Exception;
use mysqli;
use mysqli_sql_exception;

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
	 * Escape and quote string
	 */
	public function EscapeAndQuote(string $string): string
	{
		return '"' . $this->Escape($string) . '"';
	}

	/**
	 * Internal use only
	 *
	 * Convert filter array to where clause string
	 */
	protected function FilterToWhereClauseString(array $filter): string
	{
		if (!empty($filter)) {
			$stringFilter = [];

			foreach ($filter as $key => $value) {
				if (is_array($value)) {
					array_push($stringFilter, $key . " " . $value[0] . " " . $this->EscapeAndQuote($value[1]));
				} else {
					array_push($stringFilter, $key . " = " . $this->EscapeAndQuote($value));
				}
			}

			return join(' AND ', $stringFilter);
		}

		return '';
	}

	/**
	 * Connect
	 */
	public function __construct(array $data)
	{
		$this->connection = new mysqli($data['host'], $data['username'], $data['password'], $data['database'], $data['port']);
	}

	/**
	 * Transaction
	 */
	public function Transaction(Closure $transactionCallback): void
	{
		$this->connection->begin_transaction();

		try {
			$transactionCallback($this->connection);

			$this->connection->commit();
		} catch (mysqli_sql_exception $exception) {
			$this->connection->rollback();

			throw $exception;
		}
	}

	/**
	 * Select data
	 */
	public function Get(string $tableName, array $selects, array $filter, ?array $options = []): array
	{
		$sql = "SELECT " . join(', ', $selects) . " FROM $tableName";

		if (!empty($filter)) {
			$sql .= " WHERE " . $this->FilterToWhereClauseString($filter);
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
	public function Insert(string $tableName, array $data, ?array $options = []): bool
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

	/**
	 * Update data
	 */
	public function Update(string $tableName, array $data, array $filter, ?array $options = []): bool
	{
		if (empty($data)) {
			throw new Exception('Update data cannot be empty');

			return false;
		}

		$stringData = [];

		foreach ($data as $key => $value) {
			array_push($stringData, $key . " = " . $this->EscapeAndQuote($value));
		}

		$sql = "UPDATE $tableName SET " . join(', ', $stringData);

		if (!empty($filter)) {
			$sql .= " WHERE " . $this->FilterToWhereClauseString($filter);
		}

		$result = $this->connection->query($sql);

		return $result;
	}

	/**
	 * Delete data
	 */
	public function Delete(string $tableName, array $filter, ?array $options = []): bool
	{
		$sql = "DELETE FROM $tableName";

		if (!empty($filter)) {
			$sql .= " WHERE " . $this->FilterToWhereClauseString($filter);
		}

		$result = $this->connection->query($sql);

		return $result;
	}
}
