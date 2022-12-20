<?php

namespace Scripts\Console;

class Console
{
	/**
	 * Standard out
	 */
	protected $output;

	/**
	 * Error out
	 */
	protected $error;

	/**
	 * Arguments
	 */
	public Arguments $arguments;

	/**
	 * Populate arguments
	 * 
	 * @param stream $output Standard output default: STDOUT
	 * @param stream $error Standard error default: STDERR
	 */
	function __construct(array $argv, $output = STDOUT, $error = STDERR)
	{
		$this->output = $output;
		$this->error = $error;

		// Arguments
		$this->arguments = new Arguments($argv);
	}

	/**
	 * Write to output
	 */
	public function write(string $text, array | null $formatTypes = null)
	{
		if ($formatTypes === null || empty($formatTypes)) $formatTypes = [TextFormatter::DEFAULT_FOREGROUND];

		fwrite($this->output, TextFormatter::Format($text, $formatTypes));
	}

	/**
	 * Write empty line to output
	 */
	public function writeEmptyLine()
	{
		$this->write('');
	}

	/**
	 * Write to error
	 */
	public function writeError(string $text)
	{
		fwrite($this->error, TextFormatter::Format($text, [TextFormatter::RED_FOREGROUND]));
	}

	/**
	 * Serialize object or array to string
	 */
	public static function Serialize(mixed $object)
	{
		return print_r($object, true);
	}
}
