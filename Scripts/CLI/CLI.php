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
