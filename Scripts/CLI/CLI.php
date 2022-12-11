<?php

namespace Scripts\CLI;

class CLI
{
	protected $output;
	protected $error;
	public Arguments $arguments;

	function __construct(array $argv, $output = STDOUT, $error = STDERR)
	{
		$this->output = $output;
		$this->error = $error;

		// Arguments
		$this->arguments = new Arguments($argv);
	}


	public function write(string $text, array|null $formatTypes = null)
	{
		if ($formatTypes === null || empty($formatTypes)) $formatTypes = [TextFormatter::$DEFAULT_FOREGROUND];

		fwrite($this->output, TextFormatter::Format($text, $formatTypes));
	}

	public function writeLine(string $text, array|null $formatTypes = null)
	{
		$this->write($text . PHP_EOL, $formatTypes);
	}

	public function writeError(string $text)
	{
		fwrite($this->error, TextFormatter::Format($text, [TextFormatter::$RED_FOREGROUND]));
	}

	public function writeErrorLine(string $text)
	{
		$this->writeError($text . PHP_EOL);
	}

	public static function Serialize(mixed $object)
	{
		return print_r($object, true);
	}
}
