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


	public function write(string $text, int $colorType = null)
	{
		if ($colorType === null) $colorType = Colorize::$DEFAULT;

		fwrite($this->output, Colorize::Colorize($text, $colorType));
	}

	public function writeLine(string $text, int $colorType = null)
	{
		$this->write($text . PHP_EOL, $colorType);
	}

	public function writeError(string $text)
	{
		fwrite($this->error, Colorize::Colorize($text, Colorize::$RED));
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
