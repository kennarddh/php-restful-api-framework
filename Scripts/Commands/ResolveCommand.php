<?php

namespace Scripts\Commands;

use Scripts\Console\ConsoleSingleton;
use Scripts\Commands\Traits\{ExecuteTrait, NeedOtherCommandsExecuteTrait};
use Scripts\Console\TextFormatter;

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
	 */
	public function resolve()
	{
		$console = ConsoleSingleton::GetConsole();

		if (!isset($console->arguments->arguments[0])) {
			$console->writeError("Command name is required");

			return;
		}

		$commandName = $console->arguments->arguments[0];

		foreach ($this->availableCommands as $command) {
			if ($command::$name === $commandName) {
				$usedTraits = (array) class_uses($command);

				if (in_array(ExecuteTrait::class, $usedTraits, true))
					$command::execute();
				else if (in_array(NeedOtherCommandsExecuteTrait::class, $usedTraits, true))
					$command::execute($this->availableCommands);
				else {
					$console->write("Command $commandName doesn't use execute trait", [TextFormatter::YELLOW_FOREGROUND]);

					return;
				}

				return;
			}
		}

		$console->writeError("No command named $commandName");
	}
}
