<?php

namespace Scripts\CLI;

class CLI
{
	protected $output;
	protected $error;
	public array $argv;

	function __construct(array $argv, $output = STDOUT, $error = STDERR)
	{
		// Remove script name from argv
		array_shift($argv);

		$this->output = $output;
		$this->error = $error;
		$this->argv = $argv;
	}

	public function write(string $text)
	{
		fwrite($this->output, $text);
	}

	public function writeLine(string $text)
	{
		$this->write($text . PHP_EOL);
	}

	public function writeError(string $text)
	{
		fwrite($this->error, $text);
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
