<?php

namespace Scripts\Commands\Help;

use Scripts\Commands\BaseCommand;
use Scripts\Commands\Traits\NeedOtherCommandsExecuteTrait;
use Scripts\Console\ConsoleSingleton;

class Help extends BaseCommand
{
	use NeedOtherCommandsExecuteTrait;

	/**
	 * Command name
	 */
	public static string $name = 'help';

	/**
	 * Command description
	 */
	public static string $description = 'Show this';

	/**
	 * Arguments for help command
	 */
	public static $arguments = [];

	public static function execute(array $otherCommands)
	{
		$console = ConsoleSingleton::GetConsole();

		$count = 0;

		foreach ($otherCommands as $command) {
			$count += 1;

			$console->write($command::$name);
			$console->write("  " . $command::$description);

			$argumentKeys = array_map('strlen', array_keys($command::$arguments));

			// Array max function require minimum 1 value
			if (count($argumentKeys) === 0) $argumentKeys = [''];

			$maxLength = max($argumentKeys);

			if (gettype($maxLength) === 'integer') {
				$console->write("  Arguments:");

				$maxLength += 1;
			}

			foreach ($command::$arguments as $argumentKey => $argumentValue) {
				$console->write('    ' . str_pad($argumentKey, $maxLength) . ': ' . $argumentValue);
			}

			if (count($otherCommands) !== $count)
				$console->writeEmptyLine();
		}
	}
}
