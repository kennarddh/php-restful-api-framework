<?php

namespace Scripts\Console;

use InvalidArgumentException;

class Arguments
{
	/**
	 * Arguments passed as "argumentName"
	 * 
	 * Without dash or double dash
	 */
	public array $arguments = [];

	/**
	 * Named arguments
	 * 
	 * Arguments passed as "--key=value" or "-key value"
	 */
	public array $namedArguments = [];

	/**
	 * Options or flags
	 * 
	 * Arguments that passed as "--name" this will set as name => true
	 * If passed as "-name" this will set n => true, a => true, m => true, and e => true
	 */
	public array $options = [];

	function __construct($argv = [])
	{
		$this->parseArguments($argv);
	}

	/**
	 * Internal use only
	 * 
	 * Parse raw arguments and set instance property
	 * 
	 * @throws InvalidArgumentException
	 */
	private function parseArguments(array $raw): void
	{
		// Remove script name from argv
		array_shift($raw);

		$skipNext = false;
		$index = -1;

		foreach ($raw as $value) {
			$index += 1;

			if ($skipNext) {
				$skipNext = false;

				continue;
			}

			if (substr($value, 0, 2) === '--') {
				$namedRemovedPrefix = substr($value, 2);

				if (str_contains($namedRemovedPrefix, '=')) {
					// Named arguments
					[$namedKey, $namedValue] = explode('=', $namedRemovedPrefix, 2);

					$this->namedArguments[$namedKey] = $namedValue;
				} else {
					// Options
					$this->options[$namedRemovedPrefix] = true;
				}
			} elseif (substr($value, 0, 1) === '-') {
				$namedRemovedPrefix = substr($value, 1);

				if (str_contains($namedRemovedPrefix, '=')) {
					throw new InvalidArgumentException('Single hyphen (-) argument cannot contain = character');

					return;
				}

				if (isset($raw[$index + 1])) {
					if (!str_contains($raw[$index + 1], '-')) {
						// If after single dash argument is a string this will set as named argument and skip parsing next as argument
						$skipNext = true;

						$this->namedArguments[$namedRemovedPrefix] = $raw[$index + 1];

						continue;
					}
				}

				// Cannot have empty single dash
				if ($namedRemovedPrefix === '')
					throw new InvalidArgumentException('Single hyphen (-) option empty');

				// Cannot contain - character
				if (str_contains($namedRemovedPrefix, '-'))
					throw new InvalidArgumentException('Single hyphen (-) option argument cannot contain - character');

				// Set as multiple options for every charcter
				foreach (str_split($namedRemovedPrefix) as $char) {
					$this->options[$char] = true;
				}
			} else {
				// Arguments
				array_push($this->arguments, $value);
			}
		}
	}
}
