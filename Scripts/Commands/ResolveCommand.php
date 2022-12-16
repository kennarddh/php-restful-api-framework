<?php

namespace Scripts\Commands;

use InvalidArgumentException;
use Scripts\Console\ConsoleSingleton;

class ResolveCommand
{
	/**
	 * Array of commands
	 */
	public array $availableCommands;

	/** 
	 * Internal use only
	 */
	public function __construct(array $availableCommands)
	{
		$this->availableCommands = $availableCommands;
	}

	/**
	 * Resolve and run command
	 * 
	 * @throws InvalidArgumentException
	 */
	public function resolve()
	{
		$console = ConsoleSingleton::GetConsole();

		if (!isset($console->arguments->arguments[0])) {
			throw new InvalidArgumentException('Command name is required');

			return;
		}

		$commandName = $console->arguments->arguments[0];

		foreach ($this->availableCommands as $command) {
			if ($command::$name === $commandName) {
				$commandInstance = new $command($console);

				$commandInstance->execute();

				return;
			}
		}

		$console->writeErrorLine("No command named $commandName");
	}
}
