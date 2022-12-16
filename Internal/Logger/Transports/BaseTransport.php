<?php

namespace Internal\Logger\Transports;

/**
 * All logger transport must extend BaseTransport
 */
abstract class BaseTransport {
	/**
	 * Is transport silent
	 */
	protected bool $silent = false;

	/**
	 * Array of levels
	 * 
	 * If log level is in accept levels log will be forwarded to this transport
	 */
	protected array $acceptLevels;

	/**
	 * This method is called when a new log level is in the accept levels array and transport is not silent
	 */
	abstract protected function log(string $level, string $message);

	public function __construct(array $options)
	{
		$this->silent = $options['silent'];
		$this->acceptLevels = $options['acceptLevels'];
	}
}