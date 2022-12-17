<?php

namespace Internal\Logger;

use Exception;
use Internal\Logger\Transports\BaseTransport;

class Logger
{
	/**
	 * Internal use only
	 * 
	 * Array of transports
	 */
	protected static array $transports = [];

	/**
	 * Internal use only
	 * 
	 * Array of available levels
	 * 
	 * Default levels are error, warning, info, verbose, debug, internal
	 */
	protected static array $levels = ['error', 'warning', 'info', 'verbose', 'debug', 'internal'];

	public static function AddTransports(BaseTransport $transport): self
	{
		array_push(self::$transports, $transport);

		return new static;
	}

	/**
	 * Log message to transports
	 */
	public static function Log(string $level, string $message, array $data = []): void
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

			$previousFormatResult = $message;
			$modifiedData = $data;

			// Run all transformers sequentially
			foreach ($transport->transformers as $transformer) {
				$modifiedData = $transformer->transform($level, $message, $data, $modifiedData);
			}

			// Run all formatters sequentially
			foreach ($transport->formatters as $formatter) {
				$previousFormatResult = $formatter->format($level, $message, $modifiedData, $previousFormatResult);
			}

			$transport->log($level, $message, $modifiedData, $previousFormatResult);
		}
	}
}
