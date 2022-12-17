<?php

namespace Internal\Logger\Formatters;

/**
 * All logger formatter must extend BaseFormatter
 */
abstract class BaseFormatter
{
	/**
	 * This method is called when a new log logged
	 * 
	 * Return value from previous formatter is passed to current formatter
	 * 
	 * Serialize level, data, and message to string
	 */
	abstract public function format(string $level, string $message, array $data, string $previous): string;
}
