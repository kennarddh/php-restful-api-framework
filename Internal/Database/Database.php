<?php

namespace Internal\Database;

use Closure;
use Internal\Database\Adapters\BaseAdapter;

/**
 * Database connection
 */
class Database
{
	private static BaseAdapter $adapter;

	public static function SetAdapter(BaseAdapter $adapter)
	{
		self::$adapter = $adapter;
	}

	/**
	 * Select data
	 */
	public static function Get(string $tableName, array $selects, array $filter, ?array $options = []): array
	{
		return self::$adapter->Get($tableName, $selects, $filter, $options);
	}

	/**
	 * Insert new data
	 */
	public static function Insert(string $tableName, array $data, ?array $options = []): bool
	{
		return self::$adapter->Insert($tableName, $data, $options);
	}

	/**
	 * Update data
	 */
	public static function Update(string $tableName, array $data, array $filter, ?array $options = []): bool
	{
		return self::$adapter->Update($tableName, $data, $filter, $options);
	}

	/**
	 * Delete data
	 */
	public static function Delete(string $tableName, array $filter, ?array $options = []): bool
	{
		return self::$adapter->Delete($tableName, $filter, $options);
	}

	/**
	 * Transaction
	 */
	public static function Transaction(Closure $transactionCallback): void
	{
		self::$adapter->Transaction($transactionCallback);
	}
}
