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

	abstract protected function log(string $level, string $message);

	/**
	 * Every transport must call BaseTransport constructor
	 */
	public function __construct(array $options)
	{
		$this->silent = $options['silent'];
		$this->acceptLevels = $options['acceptLevels'];
	}
}