<?php

namespace Internal\Logger\Transports;

/**
 * All logger transport must extend BaseTransport
 */
abstract class BaseTransport
{
	/**
	 * Is transport silent
	 */
	public bool $silent = false;

	/**
	 * Array of levels
	 * 
	 * If log level is in accept levels log will be forwarded to this transport
	 */
	public array $acceptLevels;

	/**
	 * All level, message, and data are passed to formatters sequentially before passed to transport
	 */
	public array $formatters;

	/**
	 * This method is called when a new log level is in the accept levels array and transport is not silent
	 */
	abstract public function log(string $level, string $message, string $formattedMessage): void;

	public function __construct(array $options = [])
	{
		$this->silent = $options['silent'] ?? false;
		$this->acceptLevels = $options['acceptLevels'] ?? [];
		$this->formatters = $options['formatters'] ?? [];
	}
}
