<?php

namespace Scripts\Commands;

use Exception;
use InvalidArgumentException;
use Scripts\CLI\CLI;

class ResolveCommand
{
	/**
	 * Internal use only
	 * 
	 * Used for reference to current cli instance
	 */
	public CLI $cli;

	/**
	 * Array of commands
	 */
	public array $availableCommands;

	/** 
	 * Internal use only
	 * 
	 * Inject cli instance to command
	 */
	public function __construct(CLI $cli, array $availableCommands)
	{
		$this->cli = $cli;

		$this->availableCommands = $availableCommands;
	}

	/**
	 * Resolve and run command
	 * 
	 * @throws InvalidArgumentException
	 */
	public function resolve()
	{
		if (!isset($this->cli->arguments->arguments[0])) {
			throw new InvalidArgumentException('Command name is required');

			return;
		}

		$commandName = $this->cli->arguments->arguments[0];

		foreach ($this->availableCommands as $command) {
			if ($command::$name === $commandName) {
				$commandInstance = new $command($this->cli);
				
				$commandInstance->execute();

				return;
			}
		}

		$this->cli->writeErrorLine("No command named $commandName");
	}
}
