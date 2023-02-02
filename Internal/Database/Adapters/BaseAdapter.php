<?php

namespace Internal\Database\Adapters;

/**
 * Internal use only
 *
 * All database adapters must extend BaseAdapter
 */
abstract class BaseAdapter
{
	protected $connection;

	public abstract function __construct(array $data);

	public abstract function Get(string $tableName, array $selects, array $filter): array;

	// public abstract function Insert(string $tableName, array $data): bool;

	// public abstract function Update(string $tableName, array $filter, array $data): bool;

	// public abstract function Delete(string $tableName, array $filter): bool;
}
