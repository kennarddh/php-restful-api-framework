<?php

namespace Internal\Database\Adapters;

use Exception;

/**
 * Internal use only
 *
 * All database adapters must extend BaseAdapter
 */
abstract class BaseAdapter
{
	/**
	 * Connection object
	 */
	protected $connection;

	/**
	 * Connect
	 */
	public abstract function __construct(array $data);

	/**
	 * Select data
	 */
	public abstract function Get(string $tableName, array $selects, array $filter): array|Exception;

	/**
	 * Insert new data
	 */
	public abstract function Insert(string $tableName, array $data): bool|Exception;

	/**
	 * Update data
	 */
	public abstract function Update(string $tableName, array $data, array $filter): bool|Exception;

	/**
	 * Delete data
	 */
	public abstract function Delete(string $tableName, array $filter): bool|Exception;
}
