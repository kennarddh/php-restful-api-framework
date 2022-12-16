<?php

namespace Internal\Logger;

use Exception;

class Logger
{
	/**
	 * Internal use only
	 * 
	 * Array of transports
	 */
	protected static array $transports;

	/**
	 * Internal use only
	 * 
	 * Array of available levels
	 * 
	 * Default levels are error, warning, info, verbose, debug, internal
	 */
	protected static array $levels = ['error', 'warning', 'info', 'verbose', 'debug', 'internal'];

	public static function AddTransports(array ...$transports): self
	{
		self::$transports = array_merge(self::$transports, $transports);

		return new static;
	}

	/**
	 * Log message to transports
	 */
	public static function Log(string $level, string $message)
	{
		if (!in_array($level, self::$levels)) {
			throw new Exception("Level $level doesn't exist in logger levels");

			return;
		}

		foreach (self::$transports as $transport) {
			if (!(in_array($level, $transport->acceptLevels) && !$transport->silent)) {
				// Transport is silent or doesn't accept level

				continue;
			}

			$transport->log($level, $message);
		}
	}
}
