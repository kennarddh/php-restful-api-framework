<?php

namespace Scripts\CLI;

use InvalidArgumentException;

class Arguments
{
	public array $arguments = [];
	public array $namedArguments = [];
	public array $options = [];

	function __construct($argv = [])
	{
		$this->parseArguments($argv);
	}

	public function parseArguments(array $raw): void
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
					$this->options[$namedRemovedPrefix] = true;
				}
			} elseif (substr($value, 0, 1) === '-') {
				$namedRemovedPrefix = substr($value, 1);

				if (str_contains($namedRemovedPrefix, '=')) {
					throw new InvalidArgumentException('Single dash argument cannot contain = character');

					return;
				}

				if (isset($raw[$index + 1])) {
					if (!str_contains($raw[$index + 1], '-')) {
						$skipNext = true;

						$this->namedArguments[$namedRemovedPrefix] = $raw[$index + 1];

						continue;
					}
				}

				if ($namedRemovedPrefix === '')
					throw new InvalidArgumentException('Single dash flag empty');

				if (str_contains($namedRemovedPrefix, '-'))
					throw new InvalidArgumentException('Single dash flag argument cannot contain - character');

				foreach (str_split($namedRemovedPrefix) as $char) {
					$this->options[$char] = true;
				}
			} else {
				array_push($this->arguments, $value);
			}
		}
	}
}
